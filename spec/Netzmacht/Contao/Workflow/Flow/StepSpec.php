<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StepSpec extends ObjectBehavior
{
    const NAME = 'test';
    const LABEL = 'label';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Step');
    }

    function let()
    {
        $this->beConstructedWith(self::NAME, self::LABEL);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(self::NAME);
    }

    function it_has_a_label()
    {
        $this->getLabel()->shouldReturn(static::LABEL);
    }

    function it_uses_name_as_label_by_default()
    {
        $this->beConstructedWith(self::NAME);
        $this->getLabel()->shouldReturn(static::NAME);
    }

    function it_is_not_final_by_default()
    {
        $this->isFinal()->shouldReturn(false);
    }

    function it_can_be_final()
    {
        $this->setFinal(true)->shouldReturn($this);
        $this->isFinal()->shouldReturn(true);
    }

    function it_has_no_allowed_transitions_by_default()
    {
        $this->getAllowedTransitions()->shouldBeEqualTo(array());
    }

    function it_allows_transition()
    {
        $this->isTransitionAllowed('test')->shouldReturn(false);
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);
    }

    function it_returns_allowed_transitions()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->allowTransition('bar')->shouldReturn($this);

        $this->getAllowedTransitions()->shouldReturn(array('test', 'bar'));
    }

    function it_does_not_allow_transition_when_being_final()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);

        $this->setFinal(true);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }
}
