<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

use function count;
use function is_array;

/**
 * The transition action builder creates the forms for the actions which requires user data.
 */
final class TransitionActionsFormBuilder implements TransitionFormBuilder
{
    /**
     * The action form builders.
     *
     * @var ActionFormBuilder[]
     */
    private $builders = [];

    /**
     * @param iterable|ActionFormBuilder[] $builders The action form builders.
     */
    public function __construct(iterable $builders)
    {
        foreach ($builders as $builder) {
            $this->builders[] = $builder;
        }
    }

    public function supports(Transition $transition): bool
    {
        if (count($this->builders) === 0) {
            return false;
        }

        /** @psalm-suppress PossiblyInvalidArgument - Has to be fixed upstream */
        if (count($transition->getActions()) > 0) {
            return true;
        }

        /** @psalm-suppress PossiblyInvalidArgument - Has to be fixed upstream */
        return count($transition->getPostActions()) > 0;
    }

    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void
    {
        $formData = $formBuilder->getData();
        $data     = is_array($formData) ? $formData : [];

        foreach ($transition->getActions() as $action) {
            $data = $this->buildActionForm($action, $transition, $context, $item, $formBuilder, $data);
        }

        foreach ($transition->getPostActions() as $action) {
            $data = $this->buildActionForm($action, $transition, $context, $item, $formBuilder, $data);
        }

        if (! is_array($formData)) {
            return;
        }

        $formBuilder->setData($data);
    }

    /**
     * Build the form for an action and return the populated form data.
     *
     * @param Action              $action      The current action.
     * @param Transition          $transition  The workflow transition.
     * @param Context             $context     The current transition context.
     * @param Item                $item        The current transition item.
     * @param FormBuilder         $formBuilder The form builder.
     * @param array<string,mixed> $data        The form data.
     *
     * @return array<string,mixed>
     */
    private function buildActionForm(
        Action $action,
        Transition $transition,
        Context $context,
        Item $item,
        FormBuilder $formBuilder,
        array $data
    ): array {
        foreach ($this->builders as $builder) {
            if (! $builder->supports($action)) {
                continue;
            }

            $builder->buildForm($action, $transition, $formBuilder);

            if (! ($builder instanceof DataAwareActionFormBuilder)) {
                continue;
            }

            $data = $builder->buildFormData($action, $transition, $context, $item, $data);
        }

        return $data;
    }
}
