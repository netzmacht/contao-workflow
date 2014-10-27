<?php

namespace spec\Netzmacht\Contao\Workflow\Action;

use Netzmacht\Contao\Workflow\Action\Notify\NotificationFactory;
use Netzmacht\Contao\Workflow\Action\NotifyAction;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Step;
use Netzmacht\Contao\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

interface MockNotification
{
    public function send($tokens, $language=null);
}

/**
 * Class NotifyActionSpec
 * @package spec\Netzmacht\Contao\Workflow\Action
 *
 * @mixin NotifyAction
 */
class NotifyActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Action\NotifyAction');
        $this->shouldbeAnInstanceOf('Netzmacht\Contao\Workflow\Action');
    }

    function let(NotificationFactory $notificationFactory)
    {
        $this->beConstructedWith($notificationFactory);
    }

    function it_accepts_a_language()
    {
        $this->setLanguage('de')->shouldReturn($this);
        $this->getLanguage()->shouldReturn('de');
    }

    function it_sets_notification_id(Context $context)
    {
        $this->setNotificationId(2)->shouldReturn($this);
        $this->getNotificationId($context)->shouldReturn(2);
    }

    function it_gets_notification_id_from_data(Context $context)
    {
        $context->getProperty('id')->willReturn(5);

        $this->setNotificationId('id', true)->shouldReturn($this);
        $this->getNotificationId($context)->shouldReturn(5);
    }

    function it_sends_a_notification_during_transit(
        NotificationFactory $notificationFactory,
        MockNotification $notification,
        Entity $entity,
        Transition $transition,
        Step $stepTo,
        Context $context
    ) {
        $this->setNotificationId(2);
        $this->setLanguage('de');

        $transition->getStepTo()->willReturn($stepTo);
        $transition->getName()->willReturn('transition_name');
        $transition->getLabel()->willReturn('transition_label');

        $notificationFactory->create(2)->willReturn($notification);
        $notification->send(Argument::type('array'), 'de')->shouldBeCalled();

        $this->transit($transition, $entity, $context);
    }
}
