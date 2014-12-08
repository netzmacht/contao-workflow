<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Definition\Database;

use Netzmacht\Workflow\Contao\Definition\Definition;
use Netzmacht\Workflow\Contao\Definition\Event\CreateActionEvent;
use Netzmacht\Workflow\Contao\Definition\Event\CreateStepEvent;
use Netzmacht\Workflow\Contao\Definition\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Contao\Definition\Event\CreateWorkflowEvent;
use Netzmacht\Workflow\Contao\Definition\Exception\DefinitionException;
use Netzmacht\Workflow\Contao\Model\ActionModel;
use Netzmacht\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Contao\Model\TransitionModel;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Security\Permission;
use Model\Collection;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class WorkflowBuilder builds an workflow.
 *
 * @package Netzmacht\Workflow\Contao\Definition\Builder
 */
class WorkflowBuilder  implements EventSubscriberInterface
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
        $user = $this->getService('workflow.security.user');

        foreach ($user->getRoles() as $role) {
            if ($role->getWorkflowName() == $workflow->getName()) {
                $workflow->addRole($role);
            }
        }
    }

    /**
     * Create the steps.
     *
     * @param Workflow      $workflow The current workflow.
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
            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

            $this->steps[$model->id] = $step;
        }
    }

    /**
     * Create transitions from database.
     *
     * @param Workflow      $workflow The current workflow.
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
            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

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

            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

            if (!$event->getAction()) {
                throw new DefinitionException(sprintf('No action created for action defintion ID "%s"', $model->id));
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
     * @param Workflow      $workflow The current workflow.
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
