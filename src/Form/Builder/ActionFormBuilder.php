<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Interface ActionFormFactory describes an factory which creates an action form.
 */
interface ActionFormBuilder
{
    /**
     * Check is action type factory matches the current action.
     *
     * @param Action $action The action.
     *
     * @return bool
     */
    public function supports(Action $action): bool;

    /**
     * Build the form.
     *
     * @param Action      $action      The action.
     * @param Transition  $transition  Transition to which the action belongs.
     * @param FormBuilder $formBuilder The form builder.
     *
     * @return void
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void;
}
