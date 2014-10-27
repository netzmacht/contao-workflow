<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use Netzmacht\Contao\Workflow\ErrorCollection;
use Netzmacht\Contao\Workflow\Flow\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ContextSpec
 * @package spec\Netzmacht\Contao\Workflow\Flow
 * @mixin Context
 */
class ContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Flow\Context');
    }

    function let(InputProviderInterface $inputProvider)
    {
        $this->beConstructedWith($inputProvider);
    }

    function it_accepts_initial_properties(InputProviderInterface $inputProvider)
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith($inputProvider, $data);

        $this->getProperties()->shouldBe($data);
    }

    function it_accepts_initial_params(InputProviderInterface $inputProvider)
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith($inputProvider, array(), $data);

        $this->getParams()->shouldBe($data);
    }

    function it_has_an_input_provider(InputProviderInterface $inputProvider)
    {
        $this->getInputProvider()->shouldReturn($inputProvider);
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

    function it_sets_param()
    {
        $this->setParam('param', 'val')->shouldReturn($this);
        $this->getParam('param')->shouldReturn('val');
    }

    function it_knows_if_param_exists()
    {
        $this->hasParam('param')->shouldReturn(false);
        $this->setParam('param', 'val');
        $this->hasParam('param')->shouldReturn(true);
    }

    function it_gets_params_as_array()
    {
        $this->getParams()->shouldBeArray();
    }

    function it_add_an_errors()
    {
        $this->hasErrors()->shouldReturn(false);
        $this->addError('error', array('param' => 'foo'))->shouldReturn($this);
        $this->hasErrors()->shouldReturn(true);
    }

    function it_gets_error_collection()
    {
        $this->getErrorCollection()->shouldBeAnInstanceOf('Netzmacht\Contao\Workflow\ErrorCollection');
    }
}
