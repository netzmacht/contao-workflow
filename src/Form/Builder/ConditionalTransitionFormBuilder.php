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

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition\ConditionalTransitionAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class ConditionalTransitionFormBuilder
 */
class ConditionalTransitionFormBuilder implements TransitionFormBuilder
{
    /**
     * Transition form builder.
     *
     * @var TransitionFormBuilder
     */
    private $transitionFormBuilder;

    /**
     * ConditionalTransitionFormBuilder constructor.
     *
     * @param TransitionFormBuilder $transitionFormBuilder Transition form builder.
     */
    public function __construct(TransitionFormBuilder $transitionFormBuilder)
    {
        $this->transitionFormBuilder = $transitionFormBuilder;
    }

    /**
     * @inheritDoc
     */
    public function supports(Transition $transition) : bool
    {
        return $transition->getConfigValue('type') === 'conditional';
    }

    /**
     * @inheritDoc
     */
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder) : void
    {
        foreach ($transition->getPostActions() as $action) {
            if (! $action instanceof ConditionalTransitionAction) {
                continue;
            }

            $conditionalTransition = $action->determineMatchingTransition($item, $context);
            if (!$conditionalTransition) {
                continue;
            }

            if ($this->transitionFormBuilder->supports($conditionalTransition)) {
                $this->transitionFormBuilder->buildForm($conditionalTransition, $item, $context, $formBuilder);
            }
        }
    }
}
