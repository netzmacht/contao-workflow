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

namespace spec\Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition;

use Netzmacht\ContaoWorkflowBundle\Testing\Condition\Transition\AlwaysFalseCondition;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

/**
 * Class ConditionalTransitionActionSpec
 */
final class ConditionalTransitionActionSpec extends ObjectBehavior
{
    function it_should_execute_transition ()
    {
        // Arrange
        $entityId = EntityId::fromProviderNameAndId('phpspec', 'postactionitem1');
        $workflow = new Workflow('it_can_transit_to_step_through_postaction', 'phpspec');
        $stepname = 'postActionTargetStep';
        $step = new Step($stepname);
        $conditionalTransitionName = 'conditionalTransition';
        $conditionalTransition = new Transition($conditionalTransitionName, $workflow, $step);
        $genericTransition = new Transition('generic', $workflow, new Step('previous step'));
        $originState = new State($entityId, 'workflow', 'previous transition', 'previous step', true, [],new \DateTimeImmutable());
        $item = Item::reconstitute($entityId, $entityId, [$originState]);
        $context = new Context();
        $this->beConstructedWith('ConditionalTransitionAction1', 'Conditional transition action', [$conditionalTransition]);

        // Act
        $state = $this->transit($genericTransition, $item, $context);

        // Assert
        $postTransitionState = $item->getLatestSuccessfulState();
        expect($postTransitionState->isSuccessful())->toBe(true);
        expect($postTransitionState->getTransitionName())->toBe($conditionalTransitionName);
        expect($postTransitionState->getStepName())->toBe($stepname);
    }

    function it_should_throw_exception_when_no_transition_is_allowed()
    {
        // Arrange
        $entityId = EntityId::fromProviderNameAndId('phpspec', 'postactionitem1');
        $workflow = new Workflow('it_can_transit_to_step_through_postaction', 'phpspec');
        $stepname = 'postActionTargetStep';
        $step = new Step($stepname);
        $condition = new AlwaysFalseCondition();
        $conditionalTransitionName = 'conditionalTransition';
        $conditionalTransition = new Transition($conditionalTransitionName, $workflow, $step);
        $conditionalTransition->addPreCondition($condition);
        $genericTransition = new Transition('generic', $workflow, new Step('previous step'));
        $originState = new State($entityId, 'workflow', 'previous transition', 'previous step', true, [],new \DateTimeImmutable());
        $item = Item::reconstitute($entityId, $entityId, [$originState]);
        $context = new Context();
        $this->beConstructedWith('ConditionalTransitionAction1', 'Conditional transition action', [$conditionalTransition]);

        // Act
        $this
            ->shouldThrow(ActionFailedException::class)
            ->during('transit', [$genericTransition, $item, $context]);

        // Assert
        expect($condition->getCallCount())->toBe(1);
    }
}
