<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class DelegatingTransitionFormBuilder
 */
final class DelegatingTransitionFormBuilder implements TransitionFormBuilder
{
    /**
     * Form builders.
     *
     * @var TransitionFormBuilder[]
     */
    private $formBuilders = [];

    /**
     * DelegatingTransitionFormBuilder constructor.
     *
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

    /**
     * @inheritDoc
     */
    public function supports(Transition $transition) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder) : void
    {
        foreach ($this->formBuilders as $transitionFormBuilder) {
            if (! $transitionFormBuilder->supports($transition)) {
                continue;
            }

            $transitionFormBuilder->buildForm($transition, $item, $context, $formBuilder);
        }
    }
}
