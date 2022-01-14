<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\Workflow\Flow\Workflow;

interface WorkflowType
{
    /**
     * Get the name of the workflow.
     */
    public function getName(): string;

    /**
     * Check if workflow type matches a type name.
     *
     * @param string $typeName The type name.
     */
    public function match(string $typeName): bool;

    /**
     * Configure the workflow after at build time.
     *
     * @param Workflow $workflow The workflow.
     * @param callable $next     Callable which triggers the next workflow configuration handler. Should be called by
     *                           bye implementation to make sure what the default behaviour is applied.
     */
    public function configure(Workflow $workflow, callable $next): void;

    /**
     * Get a list of supported provider names.
     *
     * @return list<string>
     */
    public function getProviderNames(): array;
}
