<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

use function is_string;

final class ConditionalTransitionAction implements Action
{
    /**
     * Unique action name.
     *
     * @var string
     */
    private $name;

    /**
     * A list of possible transitions.
     *
     * @var list<string>
     */
    private $transitionNames;

    /**
     * The corresponding workflow.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Construct.
     *
     * @param string       $name            Unique action name.
     * @param Workflow     $workflow        The corresponding workflow.
     * @param list<string> $transitionNames A list of possible transition names.
     */
    public function __construct(string $name, Workflow $workflow, array $transitionNames)
    {
        $this->name            = $name;
        $this->transitionNames = $transitionNames;
        $this->workflow        = $workflow;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        $transition = $this->determineMatchingTransition($item, new Context());
        if ($transition) {
            return $transition->getRequiredPayloadProperties($item);
        }

        return [];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function validate(Item $item, Context $context): bool
    {
        $transition = $this->determineMatchingTransition($item, $context);
        if (! $transition) {
            $context->addError('transition.conditional.failed.no_matching_transition');

            return false;
        }

        $context->getProperties()->set($this->name, $transition->getName());

        return $transition->validate($item, $context);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ActionFailedException When no conditional transition is set in the properties.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $conditionalTransitionName = $context->getProperties()->get($this->name);
        if (! is_string($conditionalTransitionName)) {
            throw ActionFailedException::action($this, $context->getErrorCollection());
        }

        $conditionalTransition = $this->workflow->getTransition($conditionalTransitionName);
        $conditionalTransition->execute($item, $context);
    }

    /**
     * Determine the first matching transition.
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     */
    public function determineMatchingTransition(Item $item, Context $context): ?Transition
    {
        foreach ($this->transitionNames as $transitionName) {
            $transition = $this->workflow->getTransition($transitionName);
            if ($transition->isAllowed($item, $context->createCleanCopy())) {
                return $transition;
            }
        }

        return null;
    }
}
