<?php

namespace spec\Netzmacht\Contao\Workflow\Transaction;

use Netzmacht\Contao\Workflow\Event\TransactionEvent;
use Netzmacht\Contao\Workflow\Transaction\Events;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedTransactionHandlerSpec extends ObjectBehavior
{
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
                Events::TRANSACTION_BEGIN,
                Argument::type('Netzmacht\Contao\Workflow\Event\TransactionEvent')
            )
            ->shouldBeCalled();

        $this->begin();
    }

    function it_commits_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                Events::TRANSACTION_COMMIT,
                Argument::type('Netzmacht\Contao\Workflow\Event\TransactionEvent')
            )
            ->shouldBeCalled();

        $this->commit();
    }

    function it_rollbacks_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                Events::TRANSACTION_ROLLBACK,
                Argument::type('Netzmacht\Contao\Workflow\Event\TransactionEvent')
            )
            ->shouldBeCalled();

        $this->rollback();
    }
}
