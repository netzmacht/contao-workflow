<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\WorkflowChange\WorkflowChangeAction;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;

use function is_string;

final class CreateWorkflowChangeTransitionListener
{
    /**
     * Workflow Manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * @param WorkflowManager $workflowManager The workflow manager.
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * Handle the event.
     *
     * @param CreateTransitionEvent $event The event.
     *
     * @throws DefinitionException When an invalid workflow value is given.
     */
    public function onCreateTransition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();
        if ($transition->getConfigValue('type') !== 'workflow') {
            return;
        }

        if (! is_string($transition->getConfigValue('workflow'))) {
            throw new DefinitionException('Invalid target workflow defined.');
        }

        $transition->addPostAction(
            new WorkflowChangeAction($this->workflowManager, $transition->getConfigValue('workflow'))
        );
    }
}
