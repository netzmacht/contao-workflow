<?php

namespace Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Contao\Workflow\Acl\Role;
use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Flow\Transition\TransactionActionFailed;
use Netzmacht\Contao\Workflow\Form\Form;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @package Netzmacht\Contao\Workflow\Flow
 */
class Transition
{
    /**
     * The name of the transition.
     *
     * @var string
     */
    protected $name;

    /**
     * The label of the transition.
     *
     * @var string
     */
    protected $label;

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
     * Get the name of the transition.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the transition.
     *
     * @param string $name The new name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the label of the transition.
     *
     * @return string
     */
    public function getLabel()
    {
        if (!$this->label) {
            return $this->name;
        }

        return $this->label;
    }

    /**
     * Set the label of the transition.
     *
     * @param string $label The new label.
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

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
     * Set the condition.
     *
     * @param Condition $condition The new condition.
     *
     * @return $this
     */
    public function setCondition(Condition $condition)
    {
        $this->condition = $condition;

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
     * Set the precondition.
     *
     * @param Condition $preCondition The new precondition.
     *
     * @return $this
     */
    public function setPreCondition(Condition $preCondition)
    {
        $this->preCondition = $preCondition;

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
     * @param Entity  $entity  The entity.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAllowed(Entity $entity, Context $context)
    {
        if ($this->checkPreCondition($entity, $context)) {
            return $this->checkCondition($entity, $context);
        }

        return false;
    }

    /**
     * Consider if transition is available.
     *
     * If a transition can be available but it is not allowed depending on the user input.
     *
     * @param Entity  $entity  The entity.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAvailable(Entity $entity, Context $context)
    {
        if ($this->requiresInputData()) {
            return $this->checkPreCondition($entity, $context);
        }

        return $this->isAllowed($entity, $context);
    }

    /**
     * Transit an entity using this transition.
     *
     * @param Entity  $entity  The entity.
     * @param Context $context The transition context.
     *
     * @throws ProcessNotStartedException If process was not started yet.
     *
     * @return State
     */
    public function transit(Entity $entity, Context $context)
    {
        $state   = $entity->getState();
        $success = $this->isAllowed($entity, $context);


        if ($success) {
            try {
                foreach ($this->actions as $action) {
                    $action->transit($this, $entity, $context);
                }
            } catch (TransactionActionFailed $e) {
                $success = false;
                $params  = array('exception' => $e->getMessage());

                $context->addError('transition.action.failed', $params);
            }
        }

        return $state->transit($this, $context, $success);
    }

    /**
     * Check the precondition.
     *
     * @param Entity  $entity  The entity.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkPreCondition(Entity $entity, Context $context)
    {
        if (!$this->preCondition) {
            return true;
        }

        return $this->preCondition->match($this, $entity, $context);
    }

    /**
     * Check the condition.
     *
     * @param Entity  $entity  The entity.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkCondition(Entity $entity, Context $context)
    {
        if (!$this->condition) {
            return true;
        }

        return $this->condition->match($this, $entity, $context);
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
}
