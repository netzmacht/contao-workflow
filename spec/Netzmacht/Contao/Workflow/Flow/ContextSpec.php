<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Context');
    }

    function it_accepts_initial_properties()
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith($data);

        $this->getProperties()->shouldBe($data);
    }

    function it_accepts_initial_params()
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith(array(), $data);

        $this->getParams()->shouldBe($data);
    }

    function it_sets_a_param()
    {
        $this->setParam('test', 'value')->shouldReturn($this);
        $this->getParam('test')->shouldReturn('value');
    }

    function it_knows_if_param_exists()
    {
        $this->hasParam('test')->shouldReturn(false);
        $this->setParam('test', 'value');
        $this->hasParam('test')->shouldReturn(true);
    }

    function it_gets_params_as_array()
    {
        $this->getParams()->shouldBeArray();
    }

    function it_sets_property()
    {
        $this->setProperty('prop', 'val')->shouldReturn($this);
        $this->getProperty('prop')->shouldReturn('val');
    }

    function it_knows_if_property_exists()
    {
        $this->hasProperty('prop')->shouldReturn(false);
        $this->setProperty('prop', 'val');
        $this->hasProperty('prop')->shouldReturn(true);
    }

    function it_gets_properties_as_array()
    {
        $this->getProperties()->shouldBeArray();
    }
}
