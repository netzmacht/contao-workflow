<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Contao\Workflow\Data\Data;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\ErrorCollection;
use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Flow\State;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransitionSpec extends ObjectBehavior
{
    const TITLE = 'Title';
    const LABEL = 'Label';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Transition');
    }

    function it_has_a_name()
    {
        $this->setName(self::TITLE)->shouldReturn($this);
        $this->getName()->shouldReturn(self::TITLE);
    }

    function it_has_a_label()
    {
        $this->setLabel(self::LABEL)->shouldReturn($this);
        $this->getLabel()->shouldReturn(self::LABEL);
    }

    function it_is_labeled_by_name()
    {
        $this->setName(self::TITLE);
        $this->getLabel()->shouldReturn(self::TITLE);
    }

    function it_has_a_step_to(Step $step)
    {
        $this->setStepTo($step)->shouldReturn($this);
        $this->getStepTo()->shouldReturn($step);
    }

    function it_contains_actions(Action $action)
    {
        $this->addAction($action)->shouldReturn($this);
        $this->getActions()->shouldContain($action);
    }

    function it_builds_the_form(Form $form, \Netzmacht\Contao\Workflow\Action $action)
    {
        $this->addAction($action);
        $this->buildForm($form)->shouldReturn($this);

        $action->buildForm($form)->shouldBeCalled();
    }

    function it_knows_if_input_data_is_not_required(\Netzmacht\Contao\Workflow\Action $action)
    {
        $this->requiresInputData()->shouldReturn(false);

        $action->requiresInputData()->willReturn(false);
        $this->addAction($action);
        $this->requiresInputData()->shouldReturn(false);
    }

    function it_knows_if_input_data_is_required(Action $action)
    {
        $action->requiresInputData()->willReturn(true);
        $this->addAction($action);
        $this->requiresInputData()->shouldReturn(true);
    }

    function it_checks_a_precondition(Condition $condition, Entity $entity, Context $context)
    {
        $condition->match($entity, $context)->willReturn(true);

        $this->setPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($entity, $context)->shouldReturn(true);
    }

    function it_checks_a_precondition_failing(
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(false);

        $this->setPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($entity, $context)->shouldReturn(false);
    }

    function it_checks_a_condition(Condition $condition, Entity $entity, Context $context)
    {
        $condition->match($entity, $context)->willReturn(true);

        $this->setCondition($condition)->shouldReturn($this);
        $this->checkCondition($entity, $context)->shouldReturn(true);
    }

    function it_checks_a_condition_failing(
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(false);

        $this->setCondition($condition)->shouldReturn($this);
        $this->checkCondition($entity, $context)->shouldReturn(false);
    }

    function it_is_allowed_by_conditions(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(true);
        $preCondition->match($entity, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($condition);

        $this->isAllowed($entity, $context)->shouldReturn(true);
    }

    function it_is_not_allowed_by_failing_pre_condition(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(true);
        $preCondition->match($entity, $context)->willReturn(false);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAllowed($entity, $context)->shouldReturn(false);
    }

    function it_is_not_allowed_by_failing_condition(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(false);
        $preCondition->match($entity, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAllowed($entity, $context)->shouldReturn(false);
    }

    function it_is_available_when_passing_conditions(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition->match($entity, $context)->willReturn(true);
        $preCondition->match($entity, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAvailable($entity, $context)->shouldReturn(true);
    }

    function it_is_not_available_when_condition_fails(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition
            ->match($entity, $context)
            ->willReturn(false);

        $preCondition
            ->match($entity, $context)
            ->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this
            ->isAvailable($entity, $context)
            ->shouldReturn(false);
    }

    function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context
    ) {
        $condition
            ->match($entity, $context)
            ->willReturn(true);

        $preCondition
            ->match($entity, $context)
            ->willReturn(false);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this
            ->isAvailable($entity, $context)
            ->shouldReturn(false);
    }

    function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Entity $entity,
        Context $context,
        Action $action
    ) {
        $preCondition
            ->match($entity, $context)
            ->willReturn(true);

        $condition
            ->match($entity, $context)
            ->willReturn(false);

        $action->requiresInputData()->willReturn(true);
        $this->addAction($action);


        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAvailable($entity, $context)->shouldReturn(true);
    }

    function it_transits_an_entity(
        Entity $entity,
        Context $context,
        State $state,
        State $newState
    ) {
        $entity->getState()->willReturn($state);
        $state->transit($this, true, array())->willReturn($newState);

        $context->getProperties()->willReturn(array());

        $this->transit($entity, $context)->shouldBe($newState);
    }
}
