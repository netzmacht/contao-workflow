<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Definition\Database;

use Model\Collection;
use Netzmacht\Contao\Workflow\Definition\Definition;
use Netzmacht\Contao\Workflow\Definition\Event\CreateActionEvent;
use Netzmacht\Contao\Workflow\Definition\Event\CreateStepEvent;
use Netzmacht\Contao\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\Contao\Workflow\Definition\Event\CreateWorkflowEvent;
use Netzmacht\Contao\Workflow\Definition\Exception\DefinitionException;
use Netzmacht\Contao\Workflow\Model\ActionModel;
use Netzmacht\Contao\Workflow\Model\StepModel;
use Netzmacht\Contao\Workflow\Model\TransitionModel;
use Netzmacht\Contao\Workflow\ServiceContainerTrait;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class WorkflowBuilder builds an workflow.
 *
 * @package Netzmacht\Contao\Workflow\Definition\Builder
 */
class WorkflowBuilder implements EventSubscriberInterface
{
    use ServiceContainerTrait;

    /**
     * Workflow steps.
     *
     * @var Step[]
     */
    private $steps = array();

    /**
     * Workflow transitions.
     *
     * @var Transition[]
     */
    private $transitions = array();

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateWorkflowEvent::NAME => 'createWorkflow'
        );
    }

    /**
     * Handle the create workflow event.
     *
     * @param CreateWorkflowEvent $event The event being subscribed.
     *
     * @return void
     */
    public function createWorkflow(CreateWorkflowEvent $event)
    {
        $workflow = $event->getWorkflow();

        if ($workflow->getConfigValue(Definition::SOURCE) != Definition::SOURCE_DATABASE) {
            return;
        }

        $this->addRoles($workflow);
        $this->createSteps($workflow);
        $this->createTransitions($workflow);
        $this->createActions($workflow);
        $this->createProcess($workflow);

        $this->resetBuilder();
    }

    /**
     * Add permission roles to the workflow.
     *
     * @param Workflow $workflow The workflow being created.
     *
     * @return void
     */
    private function addRoles(Workflow $workflow)
    {
        /** @var User $user */
        $user = $this->getServiceContainer()->getService('workflow.security.user');

        foreach ($user->getRoles() as $role) {
            if ($role->getWorkflowName() == $workflow->getName()) {
                $workflow->addRole($role);
            }
        }
    }

    /**
     * Create the steps.
     *
     * @param Workflow $workflow The current workflow.
     *
     * @return void
     */
    private function createSteps(Workflow $workflow)
    {
        $collection = StepModel::findByWorkflow($workflow->getConfigValue('id'));

        if (!$collection) {
            return;
        }

        while ($collection->next()) {
            /** @var StepModel $model */
            $model = $collection->current();
            $step  = new Step(
                $model->name,
                $model->label,
                array_merge(
                    $collection->row(),
                    array(Definition::SOURCE => Definition::SOURCE_DATABASE)
                )
            );

            $step->setFinal($model->final);

            if ($model->limitPermission) {
                $step->setPermission(Permission::fromString($model->permission));
            }

            $workflow->addStep($step);

            $event = new CreateStepEvent($workflow, $step);
            $this->getServiceContainer()->getEventDispatcher()->dispatch($event::NAME, $event);

            $this->steps[$model->id] = $step;
        }
    }

    /**
     * Create transitions from database.
     *
     * @param Workflow $workflow The current workflow.
     *
     * @throws DefinitionException If a target step is defined which does not exiss.
     *
     * @return void
     */
    private function createTransitions(Workflow $workflow)
    {
        $collection = TransitionModel::findByWorkflow($workflow->getConfigValue('id'));

        if (!$collection) {
            return;
        }

        while ($collection->next()) {
            /** @var TransitionModel $model */
            $model      = $collection->current();
            $transition = new Transition(
                $model->name,
                $model->label,
                array_merge(
                    $collection->row(),
                    array(Definition::SOURCE => Definition::SOURCE_DATABASE)
                )
            );

            if (!isset($this->steps[$model->stepTo])) {
                throw new DefinitionException(
                    sprintf(
                        'Transition "%s" refers to step "%s" which does not exists.',
                        $transition->getName(),
                        $model->stepTo
                    )
                );
            }

            $transition->setStepTo($this->steps[$model->stepTo]);

            if ($model->limitPermission) {
                $transition->setPermission(Permission::fromString($model->permission));
            }

            $workflow->addTransition($transition);

            $event = new CreateTransitionEvent($transition);
            $this->getServiceContainer()->getEventDispatcher()->dispatch($event::NAME, $event);

            $this->transitions[$model->id] = $transition;
        }
    }

    /**
     * Load actions for all loaded transitions.
     *
     * @param Workflow $workflow The workflow being build.
     *
     * @throws DefinitionException If action could not be created for the action config.
     *
     * @return void
     */
    private function createActions(Workflow $workflow)
    {
        $collection = $this->findActions();

        if (!$collection) {
            return;
        }

        while ($collection->next()) {
            /** @var ActionModel $model */
            $model = $collection->current();
            $event = new CreateActionEvent(
                $workflow,
                $this->transitions[$model->pid],
                $model->row(),
                Definition::SOURCE_DATABASE
            );

            if ($model->postAction) {
                $event->setPostAction(true);
            }

            $this->getServiceContainer()->getEventDispatcher()->dispatch($event::NAME, $event);

            if (!$event->getAction()) {
                throw new DefinitionException(sprintf('No action created for action defintion ID "%s"', $model->id));
            } elseif ($event->isPostAction()) {
                $this->transitions[$model->pid]->addPostAction($event->getAction());
            } else {
                $this->transitions[$model->pid]->addAction($event->getAction());
            }
        }
    }

    /**
     * Find actions from database.
     *
     * @return Collection|null
     */
    private function findActions()
    {
        $transitionIds = array_keys($this->transitions);

        if (!$transitionIds) {
            return null;
        }

        $collection = ActionModel::findBy(
            array('active=1 AND pid IN (' . implode(', ', $transitionIds) . ')'),
            null,
            array(
                'order'  => 'pid, sorting',
            )
        );

        return $collection;
    }

    /**
     * Create Workflow process.
     *
     * @param Workflow $workflow The current workflow.
     *
     * @return void
     */
    private function createProcess(Workflow $workflow)
    {
        $process = deserialize($workflow->getConfigValue('process'), true);

        foreach ($process as $definition) {
            // pass not created transitions. useful to avoid errors when a transition got disabled
            if (!isset($this->transitions[$definition['transition']])) {
                continue;
            }

            if ($definition['step'] == 'start') {
                $workflow->setStartTransition($this->transitions[$definition['transition']]->getName());
            } else {
                $step       = $this->steps[$definition['step']];
                $transition = $this->transitions[$definition['transition']];

                $step->allowTransition($transition->getName());
            }
        }
    }

    /**
     * Reset the builder cache.
     *
     * @return void
     */
    private function resetBuilder()
    {
        $this->transitions = array();
        $this->steps       = array();
    }
}
