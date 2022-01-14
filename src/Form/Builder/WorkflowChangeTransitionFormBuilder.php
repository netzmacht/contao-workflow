<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\WorkflowChange\WorkflowChangeAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/** @SuppressWarnings(PHPMD.LongVariable) */
class WorkflowChangeTransitionFormBuilder implements TransitionFormBuilder
{
    /**
     * Transition form builder.
     *
     * @var TransitionFormBuilder
     */
    private $transitionFormBuilder;

    /**
     * @param TransitionFormBuilder $transitionFormBuilder Transition form builder.
     */
    public function __construct(TransitionFormBuilder $transitionFormBuilder)
    {
        $this->transitionFormBuilder = $transitionFormBuilder;
    }

    public function supports(Transition $transition): bool
    {
        return $transition->getConfigValue('type') === 'workflow';
    }

    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void
    {
        foreach ($transition->getPostActions() as $action) {
            if (! $action instanceof WorkflowChangeAction) {
                continue;
            }

            $startTransition = $action->getStartTransition();
            if (! $this->transitionFormBuilder->supports($startTransition)) {
                continue;
            }

            $this->transitionFormBuilder->buildForm($startTransition, $item, $context, $formBuilder);
        }
    }
}
