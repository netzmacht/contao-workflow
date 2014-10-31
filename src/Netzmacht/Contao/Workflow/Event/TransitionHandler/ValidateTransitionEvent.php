<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event\TransitionHandler;


use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Item;

class ValidateTransitionEvent extends AbstractTransitionHandlerEvent
{
    const NAME = 'workflow.transition.handler.validate';

    /**
     * @var Form
     */
    private $form;

    /**
     * @var bool
     */
    private $validated = true;

    /**
     * @param Workflow   $workflow
     * @param Transition $transition
     * @param Item       $item
     * @param Form       $form
     */
    public function __construct(Workflow $workflow, Transition $transition, Item $item, Form $form)
    {
        parent::__construct($workflow, $transition, $item);

        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setInvalid()
    {
        $this->validated = false;
    }

    public function isValid()
    {
        return $this->validated;
    }
}
