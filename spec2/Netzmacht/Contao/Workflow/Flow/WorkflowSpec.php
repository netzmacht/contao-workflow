<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Condition\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class WorkflowSpec
 * @package spec\Netzmacht\Contao\Workflow\Flow
 * @mixin \Netzmacht\Workflow\Flow\Workflow
 */
class WorkflowSpec extends ObjectBehavior
{
    const NAME = 'workflow';

    function let(Step $step, Transition $transition)
    {
        $transition->getName()->willReturn('start');

        $this->beConstructedWith(static::NAME, array($step), array($transition), 'start');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Workflow');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(self::NAME);
    }

    function it_has_a_label(Transition $transition)
    {
        $this->beConstructedWith(static::NAME, array(), array($transition), 'start', null, 'Label');
        $this->getLabel()->shouldReturn('Label');
    }

    function it_uses_name_as_label_if_no_label_set()
    {
        $this->getLabel()->shouldReturn(self::NAME);
    }


}
