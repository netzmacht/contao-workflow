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

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Form\Form;

/**
 * Interface Action describes an action which is executed during transition.
 *
 * @package Netzmacht\Contao\Workflow
 */
interface Action
{
    /**
     * Consider if user input is required.
     *
     * @return bool
     */
    public function requiresInputData();

    /**
     * Build the corresponding form.
     *
     * @param Form $form Transition form.
     *
     * @return void
     */
    public function buildForm(Form $form);

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Entity     $entity     The passed entity.
     * @param Context    $context    Transition context.
     *
     * @return void
     */
    public function transit(Transition $transition, Entity $entity, Context $context);
}
