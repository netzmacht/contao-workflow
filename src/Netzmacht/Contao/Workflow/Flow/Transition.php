<?php

namespace Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Contao\Workflow\Acl\Role;
use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Base;
use Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition\AndCondition;
use Netzmacht\Contao\Workflow\Flow\Transition\TransactionActionFailed;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Transition extends Base
{
    /**
     * Actions which will be executed during the transition.
     *
     * @var Action[]
     */
    private $actions = array();

    /**
     * The step the transition is moving to.
     *
     * @var Step
     */
    private $stepTo;

    /**
     * A pre condition which has to be passed to execute transition.
     *
     * @var Condition
     */
    private $preCondition;

    /**
     * A condition which has to be passed to execute the transition.
     *
     * @var Condition
     */
    private $condition;

    /**
     * A set of roles which can perform the transition.
     *
     * @var Role[]
     */
    private $roles = array();

    /**
     * The corresponding workflow.
     *
     * @var Workflow
     */
    private $workflow;


    /**
     * This method should not be called. It's used to set the workflow reference when transition is added to the
     * workflow.
     *
     * @param Workflow $workflow
     *
     * @return $this
     */
    public function setWorkflow(Workflow $workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Add an action to the transition.
     *
     * @param Action $action The added action.
     *
     * @return $this
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Get all actions.
     *
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set the target step.
     *
     * @param Step $step The target step.
     *
     * @return $this
     */
    public function setStepTo(Step $step)
    {
        $this->stepTo = $step;

        return $this;
    }

    /**
     * Get the condition.
     *
     * @return Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Add a condition.
     *
     * @param Condition $condition The new condition.
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        if (!$this->condition) {
            $this->condition = new AndCondition();
        }
        $this->condition->addCondition($condition);

        return $this;
    }

    /**
     * Get the precondition.
     *
     * @return Condition
     */
    public function getPreCondition()
    {
        return $this->preCondition;
    }

    /**
     * Add a precondition precondition.
     *
     * @param Condition $preCondition The new precondition.
     *
     * @return $this
     */
    public function addPreCondition(Condition $preCondition)
    {
        if (!$this->preCondition) {
            $this->preCondition = new AndCondition();
        }

        $this->preCondition->addCondition($preCondition);

        return $this;
    }

    /**
     * Get the target step.
     *
     * @return Step
     */
    public function getStepTo()
    {
        return $this->stepTo;
    }

    /**
     * Build the form.
     *
     * @param Form $form The form being build.
     *
     * @return $this
     */
    public function buildForm(Form $form)
    {
        foreach ($this->actions as $action) {
            $action->buildForm($form);
        }

        return $this;
    }

    /**
     * Consider if user input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        foreach ($this->actions as $action) {
            if ($action->requiresInputData()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Consider if transition is allowed.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAllowed(Item $item, Context $context)
    {
        if ($this->checkPreCondition($item, $context)) {
            return $this->checkCondition($item, $context);
        }

        return false;
    }

    /**
     * Consider if transition is available.
     *
     * If a transition can be available but it is not allowed depending on the user input.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAvailable(Item $item, Context $context)
    {
        if ($this->requiresInputData()) {
            return $this->checkPreCondition($item, $context);
        }

        return $this->isAllowed($item, $context);
    }

    public function start(Item $item, Context $context)
    {
        if ($item->isWorkflowStarted()) {
            return $item->getLatestState();
        }

        $success = $this->executeActions($item, $context);
        $state   = State::start($this, $context, $success);

        return $state;
    }

    /**
     * Transit an Item using this transition.
     *
     * @param Item   $item     The Item.
     * @param Context $context The transition context.
     *
     * @throws ProcessNotStartedException If process was not started yet.
     *
     * @return State
     */
    public function transit(Item $item, Context $context)
    {
        $state   = $item->getLatestState();
        $success = $this->executeActions($item, $context);
        $state   = $state->transit($this, $context, $success);

        $item->transit($state);

        return $state;
    }

    /**
     * Check the precondition.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkPreCondition(Item $item, Context $context)
    {
        if (!$this->preCondition) {
            return true;
        }

        return $this->preCondition->match($this, $item, $context);
    }

    /**
     * Check the condition.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkCondition(Item $item, Context $context)
    {
        if (!$this->condition) {
            return true;
        }

        return $this->condition->match($this, $item, $context);
    }

    /**
     * Add a new role.
     *
     * @param Role $role The role being added.
     *
     * @return $this
     */
    public function addRole(Role $role)
    {
        foreach ($this->roles as $assignedRole) {
            if ($assignedRole->equals($role)) {
                return $this;
            }
        }

        $this->roles[] = $role;

        return $this;
    }

    /**
     * Get all roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param Item    $item
     * @param Context $context
     *
     * @return bool
     */
    private function executeActions(Item $item, Context $context)
    {
        $success = $this->isAllowed($item, $context);

        if ($success) {
            try {
                foreach ($this->actions as $action) {
                    $action->transit($this, $item, $context);

                    return $success;
                }

                return $success;
            } catch (TransactionActionFailed $e) {
                $success = false;
                $params  = array('exception' => $e->getMessage());

                $context->addError('transition.action.failed', $params);

                return $success;
            }
        }

        return $success;
    }
}
