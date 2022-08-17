<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

interface ActionTypeFactory
{
    /**
     * Get the category.
     */
    public function getCategory(): string;

    /**
     * Get name of the action.
     */
    public function getName(): string;

    /**
     * Returns true if action as to be registered as post action.
     */
    public function isPostAction(): bool;

    /**
     * Check if workflow is supported.
     *
     * @param Workflow $workflow The workflow in which the action should be handled.
     */
    public function supports(Workflow $workflow): bool;

    /**
     * Create an action.
     *
     * @param array<string,mixed> $config     Action config.
     * @param Transition          $transition Transition to which the action belongs.
     */
    public function create(array $config, Transition $transition): Action;
}
