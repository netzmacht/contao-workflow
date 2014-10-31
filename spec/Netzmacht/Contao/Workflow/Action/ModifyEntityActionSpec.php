<?php

namespace spec\Netzmacht\Contao\Workflow\Action;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Data\Data;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Condition\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModifyEntityActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Action\ModifyEntityAction');
        $this->shouldbeAnInstanceOf('Netzmacht\Contao\Workflow\Action');
    }

    function it_sets_value()
    {
        $this->setValue('test', true)->shouldReturn($this);
        $this->getValue('test')->shouldReturn(true);
    }

    function it_has_value()
    {
        $this->hasValue('test')->shouldReturn(false);
        $this->setValue('test', true);
        $this->hasValue('test')->shouldReturn(true);
    }

    function it_removes_value()
    {
        $this->setValue('test', true);
        $this->hasValue('test')->shouldReturn(true);
        $this->removeValue('test')->shouldReturn($this);
        $this->hasValue('test')->shouldReturn(false);
    }

    function it_adds_data_mapping()
    {
        $this->hasDataMapping('test')->shouldReturn(false);
        $this->addDataMapping('test')->shouldReturn($this);
        $this->hasDataMapping('test')->shouldReturn(true);
    }

    function it_removes_data_mapping()
    {
        $this->addDataMapping('test')->shouldReturn($this);
        $this->hasDataMapping('test')->shouldReturn(true);
        $this->removeDataMapping('test')->shouldReturn($this);
        $this->hasDataMapping('test')->shouldReturn(false);
    }

    function it_updates_entity_with_values_during_transition(Transition $transition, Entity $entity, Context $context)
    {
        $this->setValue('test', true);
        $entity->setProperty('test', true)->shouldBeCalled();

        $this->transit($transition, $entity, $context);
    }

    function it_updates_entity_with_data_mapping_during_transition(Transition $transition, Entity $entity, Context $context)
    {
        $this->addDataMapping('test');
        $context->getParam('test')->willReturn(true);

        $entity->setProperty('test', true)->shouldBeCalled();
        $context->setProperty('test', true)->shouldBeCalled();

        $this->transit($transition, $entity, $context);
    }

    function it_overrides_data_mapping_with_values_during_transition(Transition $transition, Entity $entity, Context $context)
    {
        $this->setValue('test', false);
        $this->addDataMapping('test');
        $context->getParam('test')->willReturn(true);

        $entity->setProperty('test', false)->shouldBeCalled();
        $context->setProperty('test', false)->shouldBeCalled();

        $entity->setProperty('test', true)->shouldBeCalled();
        $context->setProperty('test', true)->shouldBeCalled();

        $this->transit($transition, $entity, $context);
    }
}
