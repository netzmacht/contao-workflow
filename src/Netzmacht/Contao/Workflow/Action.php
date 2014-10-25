<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow;


use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Form\Form;

interface Action
{
    /**
     * @return bool
     */
    public function requiresInputData();

    /**
     * @param Form $form
     * @return void
     */
    public function buildForm(Form $form);

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param Context $context
     * @return void
     */
    public function transit(Transition $transition, Entity $entity, Context $context);
}
