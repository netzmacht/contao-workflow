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

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\WorkflowChange\WorkflowChangeAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class ConditionalTransitionFormBuilder
 */
class WorkflowChangeTransitionFormBuilder implements TransitionFormBuilder
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
     * {@inheritDoc}
     */
    public function supports(Transition $transition) : bool
    {
        return $transition->getConfigValue('type') === 'workflow';
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder) : void
    {
        foreach ($transition->getPostActions() as $action) {
            if (! $action instanceof WorkflowChangeAction) {
                continue;
            }

            $startTransition = $action->getStartTransition();
            if ($this->transitionFormBuilder->supports($startTransition)) {
                $this->transitionFormBuilder->buildForm($startTransition, $item, $context, $formBuilder);
            }
        }
    }
}
