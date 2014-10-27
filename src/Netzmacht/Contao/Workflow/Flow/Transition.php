<?php

namespace Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Flow\Transition\TransactionActionFailed;
use Netzmacht\Contao\Workflow\Form\Form;

class Transition
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var Action[]
     */
    private $actions = array();

    /**
     * @var Step
     */
    private $stepTo;

    /**
     * @var Condition
     */
    private $preCondition;

    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var array
     */
    private $roles = array();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
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
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param Action $action
     * @return $this
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Step $step
     * @return $this
     */
    public function setStepTo(Step $step)
    {
        $this->stepTo = $step;

        return $this;
    }

    /**
     * @return Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function setCondition(Condition $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return Condition
     */
    public function getPreCondition()
    {
        return $this->preCondition;
    }

    /**
     * @param Condition $preCondition
     * @return $this
     */
    public function setPreCondition(Condition $preCondition)
    {
        $this->preCondition = $preCondition;

        return $this;
    }

    /**
     * @return Step
     */
    public function getStepTo()
    {
        return $this->stepTo;
    }

    /**
     * @param Form $form
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
     * @param Entity $entity
     * @param Context $context
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
     * @param Entity $entity
     * @param Context $context
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
     * @param Entity $entity
     * @param Context $context
     *
     * @throws ProcessNotStartedException
     *
     * @return \Netzmacht\Contao\Workflow\Flow\State
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
     * @param Entity $entity
     * @param Context $context
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
     * @param Entity $entity
     * @param Context $context
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
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function grantAccess($role)
    {
        $this->roles[] = $role;

        return $this;
    }
}
