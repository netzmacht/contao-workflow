<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Exception;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeNotFound;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

use function array_filter;
use function assert;
use function count;
use function in_array;
use function sprintf;

/**
 * Class Workflow stores callback being used by the tl_workflow table.
 */
final class WorkflowCallbackListener
{
    /**
     * Workflow type registry.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Database driven workflow loader.
     *
     * @var DatabaseDrivenWorkflowLoader
     */
    private $workflowLoader;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * @param WorkflowTypeRegistry         $typeRegistry      Workflow type registry.
     * @param RepositoryManager            $repositoryManager Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader    Database driven workflow loader.
     * @param WorkflowManager              $workflowManager   The workflow manager.
     * @param TranslatorInterface          $translator        Translator.
     */
    public function __construct(
        WorkflowTypeRegistry $typeRegistry,
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        WorkflowManager $workflowManager,
        TranslatorInterface $translator
    ) {
        $this->typeRegistry      = $typeRegistry;
        $this->repositoryManager = $repositoryManager;
        $this->workflowLoader    = $workflowLoader;
        $this->workflowManager   = $workflowManager;
        $this->translator        = $translator;
    }

    /**
     * Override the provider name if only one provider name is supported.
     *
     * @param DataContainer $dataContainer Data container driver.
     */
    public function saveProviderName(DataContainer $dataContainer): void
    {
        try {
            $repository    = $this->repositoryManager->getRepository(WorkflowModel::class);
            $workflowModel = $repository->find((int) $dataContainer->id);
            if (! $workflowModel instanceof WorkflowModel) {
                return;
            }

            $providerNames = $this->typeRegistry->getType($workflowModel->type)->getProviderNames();

            if (count($providerNames) !== 1 || $workflowModel->providerName === $providerNames[0]) {
                return;
            }

            $workflowModel->providerName = $providerNames[0];
            $repository->save($workflowModel);

            // @codingStandardsIgnoreStart
        } catch (WorkflowTypeNotFound $e) {
            // Do nothing
        }
        // @codingStandardsIgnoreEnd
    }

    /**
     * Generate a row view.
     *
     * @param array<string,mixed> $row Current data row.
     */
    public function generateRow(array $row): string
    {
        $label = sprintf(
            '<strong>%s</strong><br><span class="tl_gray">%s</span>',
            $row['label'],
            $row['description']
        );

        try {
            $this->workflowLoader->loadWorkflowById((int) $row['id']);
        } catch (Throwable $e) {
            $label .= sprintf(
                '<p class="workflow-definition-error">%s</p>',
                $this->translator->trans('workflow.invalid-workflow-definition', [$e->getMessage()], 'contao_workflow')
            );
        }

        return $label;
    }

    /**
     * Get names of workflow types.
     *
     * @return list<string>
     */
    public function getTypes(): array
    {
        return $this->typeRegistry->getTypeNames();
    }

    /**
     * Get all provider names.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return list<string>
     *
     * @throws WorkflowTypeNotFound If workflow type is not defined.
     */
    public function getProviderNames(DataContainer $dataContainer): array
    {
        if (! $dataContainer->activeRecord || ! $dataContainer->activeRecord->type) {
            return [];
        }

        if (! $this->typeRegistry->hasType($dataContainer->activeRecord->type)) {
            return [];
        }

        return $this->typeRegistry->getType($dataContainer->activeRecord->type)->getProviderNames();
    }

    /**
     * Get all start steps.
     *
     * @return array<string,array<int|string,string>>
     */
    public function getStartSteps(): array
    {
        return [
            'process' => ['start'],
            'steps'   => $this->getSteps((int) Input::get('id'), true),
        ];
    }

    /**
     * Get all end steps.
     *
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return array<string|int,string>
     */
    public function getEndSteps(DataContainer $dataContainer): array
    {
        assert($dataContainer->activeRecord !== null);

        return $this->getSteps((int) $dataContainer->activeRecord->id);
    }

    /**
     * Get all transitions.
     *
     * @return array<string|int,string>
     */
    public function getTransitions(): array
    {
        $options    = [];
        $repository = $this->repositoryManager->getRepository(TransitionModel::class);
        $collection = $repository->findBy(['.pid=?'], [Input::get('id')]);

        if ($collection) {
            foreach ($collection as $model) {
                assert($model instanceof TransitionModel);
                $label = sprintf('%s [ID %s]', $model->label, $model->id);

                switch ($model->type) {
                    case 'actions':
                        $stepTo = $model->getRelated('stepTo');
                        /** @psalm-suppress DocblockTypeContradiction - Error in Contao\Model type declaration */
                        if ($stepTo instanceof StepModel) {
                            $label .= sprintf(' --> %s [ID %s]', $stepTo->label, $stepTo->id);
                        }

                        break;

                    case 'workflow':
                        try {
                            $workflowLabel = $this->workflowManager
                                ->getWorkflowByName($model->workflow)
                                ->getLabel();

                            $workflowLabel .= sprintf(' [%s]', $model->workflow);
                        } catch (WorkflowNotFound $exception) {
                            $workflowLabel = $model->workflow;
                        }

                        $label = sprintf('%s -->  %s', $label, $workflowLabel);
                        break;

                    default:
                        // Do nothing
                }

                $options[$model->id] = $label;
            }
        }

        return $options;
    }

    /**
     * Validate given process data.
     *
     * @param mixed $value Raw process vlaue.
     *
     * @return array<mixed,mixed>|mixed
     *
     * @throws Exception If Invalid data given.
     */
    public function validateProcess($value)
    {
        $value = StringUtil::deserialize($value, true);
        $value = array_filter(
            $value,
            static function (array $item): bool {
                return $item['step'] && $item['transition'];
            }
        );

        $this->guardStartStepDefined($value);

        return $value;
    }

    /**
     * Filer and validate permission values.
     *
     * @param mixed $value The raw permissions value.
     *
     * @return list<array<string,mixed>>
     */
    public function validatePermissions($value): array
    {
        $value     = StringUtil::deserialize($value, true);
        $names     = [];
        $validated = [];

        foreach ($value as $row) {
            if (! $row['name']) {
                if (! $row['label']) {
                    continue;
                }

                $row['name'] = StringUtil::standardize($row['label']);
            }

            $this->guardValidPermissionName($row, $names);

            $names[]     = $row['name'];
            $validated[] = $row;
        }

        return $validated;
    }

    /**
     * Get steps form database.
     *
     * @param int  $parentId    The parent id.
     * @param bool $filterFinal If true only steps which are not final are loaded.
     *
     * @return array<string|int,string>
     */
    private function getSteps(int $parentId, bool $filterFinal = false): array
    {
        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);

        if ($filterFinal) {
            $collection = $repository->findBy(['.pid=?', '.final=?'], [$parentId, ''], ['order' => '.label']);
        } else {
            $collection = $repository->findBy(['.pid=?'], [$parentId], ['order' => '.label']);
        }

        if ($collection) {
            foreach ($collection as $model) {
                assert($model instanceof StepModel);

                $steps[$model->id] = $model->label;

                if (! $model->final) {
                    continue;
                }

                $steps[$model->id] .= ' [final]';
            }
        }

        return $steps;
    }

    /**
     * Guard that start step is defined.
     *
     * @param list<array<string,mixed>> $process Process information.
     *
     * @throws Exception If no start step is given.
     */
    private function guardStartStepDefined(array $process): void
    {
        if (! $process) {
            return;
        }

        $count = 0;

        foreach ($process as $definition) {
            if ($definition['step'] !== 'start') {
                continue;
            }

            $count++;
        }

        if (! $count) {
            throw new Exception('Start transition is required.');
        }

        if ($count > 1) {
            throw new Exception('There must be exactly one start transition.');
        }
    }

    /**
     * Guard that a valid permission name is given.
     *
     * @param array<string,mixed> $row   Current permission definition row.
     * @param list<string>        $names All permission names so far.
     *
     * @throws InvalidArgumentException If a invalid permission name is given.
     */
    private function guardValidPermissionName(array $row, array $names): void
    {
        $reserved = ['contao-admin', 'contao-guest'];

        if (in_array($row['name'], $names, true)) {
            throw new InvalidArgumentException(sprintf('Permission name "%s" is not unique.', $row['name']));
        }

        if (in_array($row['name'], $reserved, true)) {
            throw new InvalidArgumentException(sprintf('Permission name "%s" is reserved.', $row['name']));
        }
    }
}
