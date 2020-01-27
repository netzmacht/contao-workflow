<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class ConditionalTransitionAction
 */
final class ConditionalTransitionAction extends AbstractAction
{
    /**
     * A list of possible transitions.
     *
     * @var Transition[]
     */
    private $transitions;

    /**
     * Contains the selected conditional transition.
     *
     * @var Transition|null
     */
    private $selected_conditional_transition = null;

    /**
     * Value is set to true, when a conditional transition has been selected.
     *
     * @var bool
     */
    private $conditional_transition_has_been_chosen = false;

    /**
     * Construct.
     *
     * @param string $name      Name of the element.
     * @param string $label     Label of the element.
     * @param array  $transitions A list of possible transitions.
     * @param array  $config    Configuration values.
     */
    public function __construct(string $name, string $label, array $transitions, array $config = [])
    {
        parent::__construct($name, $label, $config);

        $this->transitions = $transitions;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [$this->getName() . '_conditionaltransition'];
    }

    /**
     * Check if conditionaltransition is required.
     *
     * @return bool
     */
    public function required(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Netzmacht\Workflow\Exception\WorkflowException
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $name = $this->getName() . '_conditionaltransition';

        $context->getProperties()->set($name, $context->getPayload()->get($name));

        $allowed_transition = $this->getFirstAllowedTransition($item, $context);
        if ($allowed_transition == null) {
            throw ActionFailedException::action($this, $context->getErrorCollection());
        }
        $allowed_transition->execute($item, $context);
    }

    private function getFirstAllowedTransition(Item $item, Context $context): ?Transition
    {
        if (!$this->conditional_transition_has_been_chosen) {
            $this->conditional_transition_has_been_chosen = true;
            foreach ($this->transitions as $transition) {
                if ($transition->isAllowed($item, $context)) {
                    $this->selected_conditional_transition = $transition;
                    break;
                }
            }
        }

        return $this->selected_conditional_transition;
    }
}
