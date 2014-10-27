<?php

namespace spec\Netzmacht\Contao\Workflow\Flow;

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
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
}
