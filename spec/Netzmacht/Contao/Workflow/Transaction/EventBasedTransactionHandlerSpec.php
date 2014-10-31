<?php

namespace spec\Netzmacht\Contao\Workflow\Transaction;

use Netzmacht\Contao\Workflow\Event\Transaction\TransactionEvent;
use Netzmacht\Contao\Workflow\Event\TransactionEvents;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedTransactionHandlerSpec extends ObjectBehavior
{
    const TRANSACTION_EVENT_CLASS = 'Netzmacht\Contao\Workflow\Transaction\Event\TransactionEvent';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Transaction\EventBasedTransactionHandler');
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Transaction\TransactionHandler');
    }

    function let(EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($eventDispatcher);
    }

    function it_begins_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                \Netzmacht\Contao\Workflow\Event\TransactionEvents::TRANSACTION_BEGIN,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->begin();
    }

    function it_commits_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                TransactionEvents::TRANSACTION_COMMIT,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->commit();
    }

    function it_rollbacks_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                \Netzmacht\Contao\Workflow\Event\TransactionEvents::TRANSACTION_ROLLBACK,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->rollback();
    }
}
