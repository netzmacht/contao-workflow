<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Action;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Transition\Condition;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransitionSpec
 * @package spec\Netzmacht\Contao\Workflow\Flow
 * @mixin Transition
 */
class TransitionSpec extends ObjectBehavior
{
    const TITLE = 'Title';
    const LABEL = 'Label';

    function let()
    {
        $this->beConstructedWith(self::TITLE, self::LABEL);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Transition');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(self::TITLE);
    }

    function it_has_a_label()
    {
        $this->getLabel()->shouldReturn(self::LABEL);
    }

    function it_is_labeled_by_name()
    {
        $this->beConstructedWith(self::TITLE, null);
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

    function it_checks_a_precondition(Condition $condition, Item $item, Context $context)
    {
        $condition->match($this, $item, $context)->willReturn(true);

        $this->setPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(true);
    }

    function it_checks_a_precondition_failing(
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);

        $this->setPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(false);
    }

    function it_checks_a_condition(Condition $condition, Item $item, Context $context)
    {
        $condition->match($this, $item, $context)->willReturn(true);

        $this->setCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(true);
    }

    function it_checks_a_condition_failing(
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);

        $this->setCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(false);
    }

    function it_is_allowed_by_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($condition);

        $this->isAllowed($item, $context)->shouldReturn(true);
    }

    function it_is_not_allowed_by_failing_pre_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(false);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    function it_is_not_allowed_by_failing_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    function it_is_available_when_passing_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    function it_is_not_available_when_condition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition
            ->match($this, $item, $context)
            ->willReturn(false);

        $preCondition
            ->match($this, $item, $context)
            ->willReturn(true);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition
            ->match($this, $item, $context)
            ->willReturn(true);

        $preCondition
            ->match($this, $item, $context)
            ->willReturn(false);

        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        Action $action
    ) {
        $preCondition
            ->match($this, $item, $context)
            ->willReturn(true);

        $condition
            ->match($this, $item, $context)
            ->willReturn(false);

        $action->requiresInputData()->willReturn(true);
        $this->addAction($action);


        $this->setCondition($condition);
        $this->setPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    function it_transits_an_entity(
        Item $item,
        Context $context,
        State $state,
        State $newState
    ) {
        $item->getLatestState()->willReturn($state);
        $item->transit($newState)->shouldBeCalled();

        $state->transit($this, $context, true)->willReturn($newState);

        $context->getProperties()->willReturn(array());

        $this->transit($item, $context)->shouldBe($newState);
    }
}
