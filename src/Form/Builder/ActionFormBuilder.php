<?php

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
     */
    public function supports(Action $action): bool;

    /**
     * Build the form.
     *
     * @param Action      $action      The action.
     * @param Transition  $transition  Transition to which the action belongs.
     * @param FormBuilder $formBuilder The form builder.
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void;
}
