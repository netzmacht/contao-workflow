<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Builder;

use Netzmacht\Workflow\Contao\Factory\Event\CreateActionEvent;
use Netzmacht\Workflow\Contao\Factory\Event\CreateStepEvent;
use Netzmacht\Workflow\Contao\Factory\Event\CreateTransitionEvent;
use Netzmacht\Workflow\Contao\Factory\Event\CreateWorkflowEvent;
use Netzmacht\Workflow\Contao\Model\ActionModel;
use Netzmacht\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Contao\Model\TransitionModel;
use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Security\Permission;
use Model\Collection;
use Netzmacht\Workflow\Security\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowBuilder extends AbstractBuilder implements EventSubscriberInterface
{
    /**
     * @var Step[]
     */
    private $steps = array();

    /**
     * @var Transition[]
     */
    private $transitions;

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
     * @param CreateWorkflowEvent $event
     */
    public function createWorkflow(CreateWorkflowEvent $event)
    {
        $workflow = $event->getWorkflow();

        if ($workflow->getConfigValue('source') != static::SOURCE_DATABASE) {
            return;
        }

        $model = $workflow->getConfigValue('model');

        $this->addRoles($workflow);
        $this->createSteps($workflow, $model);
        $this->createTransitions($workflow, $model);
        $this->createActions($workflow);
        $this->createProcess($workflow, $model);

        $this->resetBuilder();
    }


    /**
     * Add permission roles to the workflow.
     *
     * @param Workflow $workflow The workflow being created.
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
     * Create
     *
     * @param Workflow      $workflow
     * @param WorkflowModel $model
     */
    private function createSteps(Workflow $workflow, WorkflowModel $model)
    {
        $collection = StepModel::findByWorkflow($model->id);

        if (!$collection) {
            return;
        }

        while ($collection->next()) {
            /** @var StepModel $model */
            $model = $collection->current();
            $step  = new Step(
                $model->name,
                $model->label,
                array(
                    'source' => static::SOURCE_DATABASE,
                    'model'  => $collection->current()
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
     * @param Workflow      $workflow
     * @param WorkflowModel $model
     */
    private function createTransitions(Workflow $workflow, WorkflowModel $model)
    {
        $collection = TransitionModel::findByWorkflow($model->id);

        if (!$collection) {
            return;
        }

        while ($collection->next()) {
            /** @var TransitionModel $model */
            $model      = $collection->current();
            $transition = new Transition(
                $model->name,
                $model->label,
                array(
                    'source' => static::SOURCE_DATABASE,
                    'model'  => $collection->current()
                )
            );

            if (!isset($this->steps[$model->stepTo])) {
                // TODO: Throw some error.
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
     * @param Workflow $workflow
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
            $event = new CreateActionEvent($workflow, $model);
            $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

            if (!$event->getAction()) {
                // TODO: throw error
            }
            else {
                $this->transitions[$model->pid]->addAction($event->getAction());
            }
        }
    }

    /**
     * @return Collection|null
     */
    private function findActions()
    {
        $transitionIds = array_keys($this->transitions);

        if (!$transitionIds) {
            return null;
        }

        $collection = ActionModel::findAll(array(
                'column' => 'active=1 AND pid IN (' . implode(', ', $transitionIds) . ')',
                'order'  => 'pid, sorting',
            )
        );

        return $collection;
    }

    /**
     * Create Workflow process.
     *
     * @param Workflow      $workflow
     * @param WorkflowModel $model
     */
    private function createProcess(Workflow $workflow, WorkflowModel $model)
    {
        $process = deserialize($model->process, true);

        foreach ($process as $definition) {

            if ($definition['step'] == 'start') {
                $workflow->setStartTransition($this->transitions[$definition['transition']]->getName());
            }
            else {
                $step       = $this->steps[$definition['step']];
                $transition = $this->transitions[$definition['transition']];

                $step->allowTransition($transition->getName());
            }
        }
    }

    /**
     * Reset the builder cache.
     */
    private function resetBuilder()
    {
        $this->transitions = array();
        $this->steps       = array();
    }
}
