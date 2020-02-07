<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\WorkflowChange\WorkflowChangeAction;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use function is_string;

/**
 * Class CreateWorkflowChangeTransitionListener
 */
final class CreateWorkflowChangeTransitionListener
{
    /**
     * Workflow Manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * CreateWorkflowChangeTransitionListener constructor.
     *
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
     * @return void
     *
     * @throws DefinitionException When an invalid workflow value is given.
     */
    public function onCreateTransition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();
        if ($transition->getConfigValue('type') !== 'workflow') {
            return;
        }

        if (!is_string($transition->getConfigValue('workflow'))) {
            throw new DefinitionException('Invalid target workflow defined.');
        }

        $transition->addPostAction(
            new WorkflowChangeAction($this->workflowManager, $transition->getConfigValue('workflow'))
        );
    }
}
