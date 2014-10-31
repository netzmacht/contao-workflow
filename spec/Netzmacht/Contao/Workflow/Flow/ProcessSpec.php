<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\ErrorCollection;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Condition\Transition;
use Netzmacht\Contao\Workflow\Flow\Condition\Workflow\Condition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WorkflowSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Workflow');
    }

    function let(Step $step, Step $startStep,  Transition $transition, Transition $startTransition, Condition $condition)
    {
        $startTransition->getName()->willReturn('start');
        $transition->getName()->willReturn('test');

        $step->getName()->willReturn('step');
        $startStep->getName()->willReturn('start');
        $startStep->isTransitionAllowed('test')->willReturn(true);

        $transitions = array($startTransition, $transition);
        $steps = array($startStep, $step);

        $this->beConstructedWith($steps, $transitions, 'start', $condition);
    }

    function it_has_a_name()
    {
        $this->setName('workflow')->shouldReturn($this);
        $this->getName()->shouldReturn('workflow');
    }

    function it_gets_a_transition_by_name(Transition $transition)
    {
        $this
            ->getTransition('test')
            ->shouldReturn($transition);
    }

    function it_throws_when_transition_not_found()
    {
        $this
            ->shouldThrow('Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotFoundException')
            ->duringGetTransition('test2');
    }

    function it_gets_a_step_by_name(Step $step)
    {
        $this->getStep('step')->shouldReturn($step);
    }

    function it_has_a_start_transition(Transition $startTransition)
    {
        $this->getStartTransition()->shouldReturn($startTransition);
    }

    function it_throws_when_step_not_found()
    {
        $this
            ->shouldThrow('Netzmacht\Contao\Workflow\Flow\Exception\StepNotFoundException')
            ->duringGetStep('test2');
    }

    function it_matches_an_entity(Entity $entity, Condition $condition)
    {
        $condition->match($entity)->willReturn(true);

        $this->match($entity)->shouldReturn(true);
    }

    function it_does_not_match_an_entity_when_condition_fails(Entity $entity, Condition $condition)
    {
        $condition->match($entity)->willReturn(false);

        $this->match($entity)->shouldReturn(false);
    }

    function it_transits_an_entity(
        Entity $entity,
        ErrorCollection $errorCollection,
        State $state,
        State $startState,
        Transition $transition
    ) {
        $startState->getStepName()->willReturn('start');
        $entity->getState()->willReturn($startState);

        $transition
            ->transit($entity, Argument::type('Netzmacht\Contao\Workflow\Flow\Context'), $errorCollection)
            ->willReturn($state);

        $this
            ->transit($entity, 'test', $errorCollection)
            ->shouldBeAnInstanceOf('Netzmacht\Contao\Workflow\Model\State');
    }

    function it_throws_when_process_is_not_started(
        Entity $entity,
        ErrorCollection $errorCollection,
        State $state,
        Transition $transition
    )
    {
        $entity->getState()->willReturn(null);

        $transition
            ->transit($entity, Argument::type('Netzmacht\Contao\Workflow\Flow\Context'), $errorCollection)
            ->willReturn($state);

        $this
            ->shouldThrow('Netzmacht\Contao\Workflow\Flow\Exception\ProcessNotStartedException')
            ->duringTransit($entity, 'test', $errorCollection);
    }

    function it_throws_when_transition_is_not_allowed(
        Entity $entity,
        ErrorCollection $errorCollection,
        State $state,
        State $startState,
        Transition $transition,
        Step $startStep
    ) {
        $startState->getStepName()->willReturn('start');
        $entity->getState()->willReturn($startState);

        $startStep->isTransitionAllowed('test')->willReturn(false);

        $transition
            ->transit($entity, Argument::type('Netzmacht\Contao\Workflow\Flow\Context'), $errorCollection)
            ->willReturn($state);

        $this
            ->shouldThrow('Netzmacht\Contao\Workflow\Flow\Exception\TransitionNotAllowedException')
            ->duringTransit($entity, 'test', $errorCollection);
    }

    function it_starts_a_new_workflow(
        Entity $entity,
        ErrorCollection $errorCollection,
        Transition $startTransition,
        State $state
    ) {
        $startTransition
            ->transit($entity, Argument::type('Netzmacht\Contao\Workflow\Flow\Context'), $errorCollection)
            ->willReturn($state);

        $this->start($entity, $errorCollection)->shouldBeAnInstanceOf('Netzmacht\Contao\Workflow\Model\State');
    }

    function it_should_return_current_state_if_already_started(
        Entity $entity,
        ErrorCollection $errorCollection,
        State $state
    ) {
        $entity->getState()->willReturn($state);

        $this->start($entity, $errorCollection)->shouldBeAnInstanceOf($state);
    }
}
