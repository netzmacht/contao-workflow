<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated\Action;


use Netzmacht\Contao\WorkflowDeprecated\Action;
use Netzmacht\Contao\WorkflowDeprecated\Action\Condition\Condition;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Netzmacht\Contao\WorkflowDeprecated\View\Form;
use Netzmacht\Contao\WorkflowDeprecated\View\FormFactory;

abstract class AbstractAction implements Action
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Condition
     */
    protected $preCondition;

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function validate(Transition $transition, Entity $entity)
    {
        if ($this->form) {
            return $this->form->validate();
        }

        return true;
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function match(Transition $transition, Entity $entity)
    {
        if ($this->preCondition) {
            return $this->preCondition->match($this, $transition, $entity);
        }

        return true;
    }

    /**
     * @param FormFactory $formFactory
     * @return $this
     */
    public function buildForm(FormFactory $formFactory)
    {
        $this->form = $formFactory->create();

        return $this;
    }

    /**
     * @return bool
     */
    public function hasForm()
    {
        return !empty($this->form);
    }

} 