<?php

namespace spec\Netzmacht\Contao\Workflow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Entity\EntityRepository;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Model\State;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Model\StateRepository;
use Netzmacht\Contao\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition';

    const FORM_CLASS = 'Netzmacht\Contao\Workflow\Form\Form';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\TransitionHandler');
    }

    function let(
        Entity $entity,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityRepository $entityRepository,
        TransactionHandler $transactionHandler,
        Context $context
    )  {
        $this->beConstructedWith(
            $entity,
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

    function it_gets_entity(Entity $entity)
    {
        $this->getEntity()->shouldReturn($entity);
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

    function it_gets_transition_name_for_nonstart_transition(Workflow $workflow, Transition $transition, Entity $entity, State $state)
    {
        $entity->getState()->willReturn($state);
        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }

    function it_knows_about_nonstart_transition(Entity $entity, State $state)
    {
        $entity->getState()->willReturn($state);
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


    function it_gets_nonstart_transition(Workflow $workflow, Transition $transition, State $state, Entity $entity)
    {
        $entity->getState()->willReturn($state);
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
