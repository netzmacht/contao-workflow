<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Factory;

use Model\Collection;
use Netzmacht\Contao\Workflow\Acl\Role;
use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;
use Netzmacht\Contao\Workflow\Contao\Model\StepModel;
use Netzmacht\Contao\Workflow\Contao\Model\TransitionModel;
use Netzmacht\Contao\Workflow\Contao\Model\WorkflowModel;
use Netzmacht\Contao\Workflow\Factory;
use Netzmacht\Contao\Workflow\Event\Factory\CreateManagerEvent;
use Netzmacht\Contao\Workflow\Event\Factory\CreateWorkflowEvent;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Flow\Condition\Workflow\ProviderTypeCondition;
use Netzmacht\Contao\Workflow\Manager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowBuilder implements EventSubscriberInterface
{
    /**
     * @var array|Step[]
     */
    private $steps = array();

    /**
     * @var array|Transition[]
     */
    private $transitions = array();

    /**
     * @var array|Role[]
     */
    private $roles = array();

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateManagerEvent::NAME  => 'handleCreateManager',
            CreateWorkflowEvent::NAME => 'handleCreateWorkflow',
        );
    }

    /**
     * @param CreateManagerEvent $event
     */
    public function handleCreateManager(CreateManagerEvent $event)
    {
        $this->createManager($event);

        $workflowType = $event->getWorkflowType();
        $providerName = $event->getProviderName();
        $manager      = $event->getManager();

        $this->createWorkflows($manager, $workflowType, $providerName);
    }

    /**
     * @param CreateWorkflowEvent $event
     */
    public function handleCreateWorkflow(CreateWorkflowEvent $event)
    {
        $workflow = $event->getWorkflow();

        $this->createDefaultConditions($workflow);

        if ($workflow->getModelId()) {
            $this->createRoles($workflow);
            $this->createSteps($workflow);
            $this->createTransitions($workflow);
            $this->createProcess($workflow);
        }
    }

    /**
     * Create workflows for a manager.
     *
     * @param Manager $manager
     * @param string  $workflowType
     * @param string  $providerName
     */
    private function createWorkflows(Manager $manager, $workflowType, $providerName)
    {
        if ($providerName) {
            $collection = WorkflowModel::findByTypeAndProvider($workflowType, $providerName);
        } else {
            $collection = WorkflowModel::findByType($workflowType);
        }

        while ($collection && $collection->next()) {
            $workflow = new Workflow(
                $collection->name,
                $collection->providerName,
                $collection->label,
                $collection->row(),
                $collection->id
            );

            $event = new CreateWorkflowEvent($workflow);
            $this->getEventDispatcher()->dispatch($event::NAME, $event);

            $manager->addWorkflow($workflow);
        }
    }

    /**
     * @param $workflow
     */
    private function createDefaultConditions(Workflow $workflow)
    {
        $workflow->addCondition(new ProviderTypeCondition());
    }

    /**
     * Create
     *
     * @param Workflow $workflow
     */
    private function createSteps(Workflow $workflow)
    {
        $collection = StepModel::findByWorkflow($workflow->getModelId());

        while ($collection && $collection->next()) {
            $step = new Step($collection->name, $collection->label, $collection->row(), $collection->id);
            $step->setFinal($collection->final);

            $workflow->addStep($step);

            $this->steps[$collection->id] = $step;
        }
    }

    /**
     * Create transitions from database.
     *
     * @param Workflow $workflow
     */
    private function createTransitions(Workflow $workflow)
    {
        $collection = TransitionModel::findByWorkflow($workflow->getModelId());

        while ($collection && $collection->next()) {
            $transition = new Transition($collection->name, $collection->label, $collection->row(), $collection->id);

            if (!isset($this->steps[$collection->stepTo])) {
                // TODO: Throw some error.
            }

            $transition->setStepTo($this->steps[$collection->stepTo]);

            $this->addRolesToTransition($collection, $transition);
            $workflow->addTransition($transition);

            $this->transitions[$collection->id] = $transition;
        }
    }

    /**
     * Create roles from database.
     *
     * @param Workflow $workflow
     */
    private function createRoles(Workflow $workflow)
    {
        $collection = RoleModel::findByWorkflow($workflow->getModelId());

        while ($collection && $collection->next()) {
            $role = new Role($collection->name, $collection->label, $collection->row(), $collection->id);
            $workflow->addRole($role);

            $this->roles[$collection->id] = $role;
        }
    }

    /**
     * Add roles to transition.
     *
     * @param Collection $collection
     * @param Transition $transition
     */
    private function addRolesToTransition($collection, Transition $transition)
    {
        $roles = deserialize($collection->roles, true);
        foreach ($roles as $role) {
            if (isset($this->roles[$role])) {
                $transition
                    ->addRole($this->roles[$role]);
                //    ->addPreCondition(new PermissionCondition($this->getAclManager()));
            }
            // TODO: Handle error
        }
    }

    /**
     * Craete process by allow transitions for the steps.
     *
     * @param Workflow $workflow
     */
    private function createProcess(Workflow $workflow)
    {
        $process = deserialize($workflow->getConfigValue('process'), true);

        foreach ($process as $definition) {

            if ($definition['step'] == 'start') {
                $workflow->setStartTransition($this->transitions[$definition['transition']]);
            }
            else {
                $step       = $this->steps[$definition['step']];
                $transition = $this->transitions[$definition['transition']];

                $step->allowTransition($transition->getName());
            }
        }
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }


    /**
     * @return \Pimple
     */
    private function getContainer()
    {
        return $GLOBALS['container'];
    }

    /**
     * @param CreateManagerEvent $event
     */
    private function createManager(CreateManagerEvent $event)
    {
        if ($event->getManager()) {
            return;
        }

        $container = $this->getContainer();
        $manager   = new Manager(
            $container['workflow.transition-handler-factory'],
            $container['workflow.entity-manager']->getStateRepository()
        );

        $event->setManager($manager);
    }
}
