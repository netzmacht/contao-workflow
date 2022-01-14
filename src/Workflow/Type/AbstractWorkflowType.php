<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\TypeCondition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AbstractWorkflowType provides default implementation for common WorkflowType methods.
 */
abstract class AbstractWorkflowType implements WorkflowType
{
    /**
     * Workflow type name.
     *
     * @var string
     */
    private $name;

    /**
     * List of supported providers.
     *
     * @var string[]|array
     */
    private $supportedProviders;

    /**
     * @param string         $name               The workflow type name.
     * @param array|string[] $supportedProviders Supported providers.
     */
    public function __construct(string $name, array $supportedProviders)
    {
        $this->name               = $name;
        $this->supportedProviders = $supportedProviders;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function match(string $typeName): bool
    {
        return $this->getName() === $typeName;
    }

    public function configure(Workflow $workflow, callable $next): void
    {
        $workflow->setConfigValue(WorkflowType::class, $this);
        $workflow->addCondition(new TypeCondition($this));
        $next();
    }

    /**
     * {@inheritDoc}
     */
    public function getProviderNames(): array
    {
        return $this->supportedProviders;
    }
}
