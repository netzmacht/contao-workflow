<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition\ConditionalTransitionAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Override;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/** @SuppressWarnings(PHPMD.LongVariable) */
final class ConditionalTransitionFormBuilder implements TransitionFormBuilder
{
    /**
     * Transition form builder.
     */
    private TransitionFormBuilder $transitionFormBuilder;

    /** @param TransitionFormBuilder $transitionFormBuilder Transition form builder. */
    public function __construct(TransitionFormBuilder $transitionFormBuilder)
    {
        $this->transitionFormBuilder = $transitionFormBuilder;
    }

    #[Override]
    public function supports(Transition $transition): bool
    {
        return $transition->getConfigValue('type') === 'conditional';
    }

    #[Override]
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void
    {
        foreach ($transition->getPostActions() as $action) {
            if (! $action instanceof ConditionalTransitionAction) {
                continue;
            }

            $conditionalTransition = $action->determineMatchingTransition($item, $context);
            if (! $conditionalTransition) {
                continue;
            }

            if (! $this->transitionFormBuilder->supports($conditionalTransition)) {
                continue;
            }

            $this->transitionFormBuilder->buildForm($conditionalTransition, $item, $context, $formBuilder);
        }
    }
}
