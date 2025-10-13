<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\TypeCondition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;

/**
 * Class AbstractWorkflowType provides default implementation for common WorkflowType methods.
 */
abstract class AbstractWorkflowType implements WorkflowType
{
    /**
     * @param string       $name               The workflow type name.
     * @param list<string> $supportedProviders Supported providers.
     */
    public function __construct(private string $name, private array $supportedProviders)
    {
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function match(string $typeName): bool
    {
        return $this->getName() === $typeName;
    }

    #[Override]
    public function configure(Workflow $workflow, callable $next): void
    {
        $workflow->setConfigValue(WorkflowType::class, $this);
        $workflow->addCondition(new TypeCondition($this));
        $next();
    }

     /** {@inheritDoc} */
    #[Override]
    public function getProviderNames(): array
    {
        return $this->supportedProviders;
    }
}
