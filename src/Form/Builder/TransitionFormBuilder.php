<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

interface TransitionFormBuilder
{
    /**
     * Check if form builder supports the transition.
     *
     * @param Transition $transition Workflow transition.
     */
    public function supports(Transition $transition): bool;

    /**
     * Build the transition form.
     *
     * @param Transition  $transition  The workflow transition.
     * @param Item        $item        The current transition item.
     * @param Context     $context     The current transition context.
     * @param FormBuilder $formBuilder The form builder.
     */
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void;
}
