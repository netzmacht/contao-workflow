<?php

declare(strict_types=1);

use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationBuilderInterface;
use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationInterface;
use Phpcq\PluginApi\Version10\DiagnosticsPluginInterface;
use Phpcq\PluginApi\Version10\EnvironmentInterface;
use Phpcq\PluginApi\Version10\Output\OutputInterface;
use Phpcq\PluginApi\Version10\Output\OutputTransformerFactoryInterface;
use Phpcq\PluginApi\Version10\Output\OutputTransformerInterface;
use Phpcq\PluginApi\Version10\Report\TaskReportInterface;

return new class implements DiagnosticsPluginInterface {
    public function getName(): string
    {
        return 'phpspec';
    }

    public function describeConfiguration(PluginConfigurationBuilderInterface $configOptionsBuilder): void
    {
        $configOptionsBuilder
            ->describeStringOption('config_file', 'The phpspec.yml configuration file')
            ->isRequired()
            ->withDefaultValue('phpspec.yml');

        $configOptionsBuilder->describeStringListOption(
            'custom_flags',
            'Any custom flags to pass to phpunit. For valid flags refer to the phpunit documentation.'
        );

        $configOptionsBuilder
            ->describeStringOption('phpspec_path', 'The path to the phpspec binary')
            ->isRequired()
            ->withDefaultValue('vendor/bin/phpspec');
    }

    public function createDiagnosticTasks(PluginConfigurationInterface $config, EnvironmentInterface $environment): iterable
    {
        $projectRoot = $environment->getProjectConfiguration()->getProjectRootPath();
        yield $environment
            ->getTaskFactory()
            ->buildPhpProcess('phpspec', $this->buildArguments($config))
            ->withWorkingDirectory($projectRoot)
            ->withOutputTransformer($this->createOutputTransformer($projectRoot, $environment))
            ->build();
    }

    private function buildArguments(PluginConfigurationInterface $config): array
    {
        $arguments = [
            $config->getString('phpspec_path'),
            'run',
            '--format=junit',
            '-c',
            $config->getString('config_file')
        ];
        if ($config->has('custom_flags')) {
            foreach ($config->getStringList('custom_flags') as $flag) {
                $arguments[] = $flag;
            }
        }

        return $arguments;
    }

    private function createOutputTransformer(string $rootDir, EnvironmentInterface $environment): OutputTransformerFactoryInterface
    {
        return new class($rootDir) implements OutputTransformerFactoryInterface {
            /** @var string */
            private $rootDir;

            public function __construct(string $rootDir) {
                $this->rootDir = $rootDir;
            }

            public function createFor(TaskReportInterface $report): OutputTransformerInterface
            {
                return new class($report, $this->rootDir) implements OutputTransformerInterface {
                    /** @var TaskReportInterface $report */
                    private $report;
                    /** @var string */
                    private $buffer = '';
                    /** @var string */
                    private $rootDir;
                    /** @var string */
                    private $errors = '';

                    public function __construct(TaskReportInterface $report, string $rootDir)
                    {
                        $this->report = $report;
                        $this->rootDir = $rootDir;
                    }

                    public function write(string $data, int $channel): void
                    {
                        switch ($channel) {
                            case OutputInterface::CHANNEL_STDOUT:
                                $this->buffer .= $data;
                                break;
                            case OutputInterface::CHANNEL_STDERR:
                                $this->errors .= $data;
                        }
                    }

                    public function finish(int $exitCode): void
                    {
                        if ($this->errors) {
                            $this->report
                                ->addAttachment('error.log')
                                ->fromString($this->errors)
                                ->setMimeType('text/plain');
                        }

                        if (!$this->buffer) {
                            return;
                        }

                        $xmlDocument = new DOMDocument('1.0');
                        $xmlDocument->loadXML($this->buffer);

                        $rootNode = $xmlDocument->firstChild;

                        if (!$rootNode instanceof DOMNode || $rootNode->nodeName !== 'testsuites') {
                            return;
                        }

                        foreach ($rootNode->childNodes as $childNode) {
                            if ((!$childNode instanceof DOMElement) || ($childNode->nodeName !== 'testsuite')) {
                                continue;
                            }
                            $this->walkTestSuite($childNode);
                        }

                        $this->report->close(
                            $exitCode === 0 ? TaskReportInterface::STATUS_PASSED : TaskReportInterface::STATUS_FAILED
                        );
                    }

                    private function walkTestSuite(DOMElement $testsuite): void
                    {
                        foreach ($testsuite->childNodes as $childNode) {
                            if (!$childNode instanceof DOMElement) {
                                continue;
                            }

                            switch ($childNode->nodeName) {
                                case 'testsuite':
                                    $this->walkTestSuite($childNode);
                                    break;
                                case 'testcase':
                                    $this->walkTestCase($childNode, $testsuite->getAttribute('name'));
                            }
                        }
                    }

                    private function walkTestCase(DOMElement $testCase, string $testSuite): void
                    {
                        $severity = $this->getSeverity($testCase);
                        if (null === $severity) {
                            return;
                        }

                        $report     = false;
                        $className  = $classFile = $testCase->getAttribute('classname');
                        $methodName = str_replace(' ', '_', $testCase->getAttribute('name'));
                        $source     = $this->getSourceInformation($className, $methodName);
                        if ($source) {
                            $source = explode(':', $source, 2);
                        }

                        foreach ($testCase->childNodes as $childNode) {
                            if (!$childNode instanceof DOMElement) {
                                continue;
                            }
                            if (! in_array($childNode->nodeName, ['failure', 'error', 'skipped'])) {
                                continue;
                            }

                            $report = true;
                            $builder = $this->report
                                ->addDiagnostic($severity, $childNode->getAttribute('message'))
                                ->forClass($testSuite)
                                ->fromSource($testCase->getAttribute('name'));

                            if ($source) {
                                $builder->forFile($this->stripRootDir($source[0]))->forRange((int) $source[1]);
                            }
                        }

                        if ($report === false) {
                            $builder = $this->report
                                ->addDiagnostic($severity, $testCase->getAttribute('name'))
                                ->forClass($testSuite)
                                ->fromSource($className);

                            if ($source) {
                                $builder->forFile($this->stripRootDir($source[0]))->forRange((int) $source[1]);
                            }
                        }
                    }

                    /** @psalm-return ?TDiagnosticSeverity */
                    private function getSeverity(DOMElement $childNode): ?string
                    {
                        switch ($childNode->getAttribute('status')) {
                            case 'passed':
                                return TaskReportInterface::SEVERITY_INFO;

                            case 'failed':
                                return TaskReportInterface::SEVERITY_MAJOR;

                            case 'broken':
                                return TaskReportInterface::SEVERITY_MINOR;

                            case 'skipped':
                            case 'pending':
                                return TaskReportInterface::SEVERITY_MARGINAL;

                            default:
                                return null;
                        }
                    }

                    private function getSourceInformation(string $className, string $methodName): ?string
                    {
                        static $cache = [];
                        if (isset($cache[$className]) && array_key_exists($methodName, $cache[$className])) {
                            return $cache[$className];
                        }

                        $command = '%1$s -r "require \'%2$s/vendor/autoload.php\';'
                            . 'echo (class_exists(%3$s::class) ? ((new ReflectionClass(%3$s::class))->getFileName() '
                            . '. \':\' . (new ReflectionMethod(%3$s::class, \'%4$s\'))->getStartLine())'
                            . ': \'\');" 2>/dev/null';
                        $command = sprintf($command, PHP_BINARY, $this->rootDir, $className, $methodName);

                        return $cache[$className][$methodName] = shell_exec($command);
                    }

                    private function stripRootDir(string $content): string
                    {
                        return str_replace($this->rootDir . '/', '', $content);
                    }
                };
            }
        };
    }
};
