<?php

namespace spec\Netzmacht\Contao\Workflow;

use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Item;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Condition\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use Netzmacht\Contao\Workflow\TransitionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransitionHandlerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin TransitionHandler
 */
class TransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition';

    const FORM_CLASS = 'Netzmacht\Contao\Workflow\Form\Form';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\TransitionHandler');
    }

    function let(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityRepository $entityRepository,
        TransactionHandler $transactionHandler,
        Context $context
    )  {
        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $context
        );
    }

    function it_gets_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_gets_item(Item $item)
    {
        $this->getItem()->shouldReturn($item);
    }

    function it_gets_transition_name_for_start_transition(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }

    function it_knows_about_start_transition()
    {
        $this->isStartTransition()->shouldReturn(true);
    }

    function it_gets_transition_name_for_not_started_transition(Workflow $workflow, Transition $transition, Item $item, State $state)
    {
        $item->isWorkflowStarted()->willReturn(true);

        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }

    function it_knows_about_nonstart_transition(Item $item, State $state)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $this->isStartTransition()->shouldReturn(false);
    }

    function it_gets_start_transition(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $this->getTransition()->shouldReturn($transition);
    }

    function it_requires_input_data(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->requiresInputData()->willReturn(true);
        $this->requiresInputData()->shouldReturn(true);
    }

    function it_does_not_requires_input_data(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->requiresInputData()->willReturn(false);
        $this->requiresInputData()->shouldReturn(false);
    }

    function it_gets_the_context(Context $context)
    {
        $this->getContext()->shouldReturn($context);
    }


    function it_gets_nonstart_transition(Workflow $workflow, Transition $transition, State $state, Item $item)
    {
//        $item->getState()->willReturn($state);
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);
    }

    function it_gets_form(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->buildForm(Argument::type(self::FORM_CLASS))->shouldBeCalled();

        $this->getForm()->shouldBeAnInstanceOf(self::FORM_CLASS);
    }

}
