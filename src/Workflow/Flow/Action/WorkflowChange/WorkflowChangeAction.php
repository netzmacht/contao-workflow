<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\WorkflowChange;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Override;

/**
 * Class WorkflowChangeAction executes the start transition of another workflow.
 */
final class WorkflowChangeAction implements Action
{
    /**
     * The workflow manager.
     */
    private WorkflowManager $workflowManager;

    /**
     * The workflow name.
     */
    private string $workflowName;

    /**
     * @param WorkflowManager $workflowManager The workflow manager containing all workflow information.
     * @param string          $workflowName    The workflow name.
     */
    public function __construct(WorkflowManager $workflowManager, string $workflowName)
    {
        $this->workflowManager = $workflowManager;
        $this->workflowName    = $workflowName;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getRequiredPayloadProperties(Item $item): array
    {
        return $this->getStartTransition()->getRequiredPayloadProperties($item);
    }

    #[Override]
    public function validate(Item $item, Context $context): bool
    {
        $startTransition = $this->getStartTransition();

        if (! $startTransition->isAllowed($item, $context)) {
            return false;
        }

        return $this->getStartTransition()->validate($item, $context);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    #[Override]
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $this->getStartTransition()->execute($item, $context);
    }

    /**
     * Get the start transition of the assigned workflow.
     */
    public function getStartTransition(): Transition
    {
        return $this->workflowManager->getWorkflowByName($this->workflowName)->getStartTransition();
    }
}
