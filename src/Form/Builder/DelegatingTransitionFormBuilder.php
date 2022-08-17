<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/** @SuppressWarnings(PHPMD.LongVariable) */
final class DelegatingTransitionFormBuilder implements TransitionFormBuilder
{
    /**
     * Form builders.
     *
     * @var TransitionFormBuilder[]
     */
    private $formBuilders = [];

    /**
     * @param TransitionFormBuilder[] $formBuilders Transition form builders
     */
    public function __construct(iterable $formBuilders = [])
    {
        foreach ($formBuilders as $formBuilder) {
            $this->register($formBuilder);
        }
    }

    /**
     * Register a transition form builder.
     *
     * @param TransitionFormBuilder $transitionFormBuilder The transition form builder.
     *
     * @reurn void
     */
    public function register(TransitionFormBuilder $transitionFormBuilder): void
    {
        $this->formBuilders[] = $transitionFormBuilder;
    }

    public function supports(Transition $transition): bool
    {
        return true;
    }

    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void
    {
        foreach ($this->formBuilders as $transitionFormBuilder) {
            if (! $transitionFormBuilder->supports($transition)) {
                continue;
            }

            $transitionFormBuilder->buildForm($transition, $item, $context, $formBuilder);
        }
    }
}
