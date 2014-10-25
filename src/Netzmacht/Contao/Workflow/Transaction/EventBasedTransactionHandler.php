<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Transaction;


use Netzmacht\Contao\Workflow\Event\TransactionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedTransactionHandler implements TransactionHandler
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * Begin transaction fires an Events::TRANSACTION_BEGIN event
     */
    public function begin()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(Events::TRANSACTION_BEGIN, $event);
    }

    /**
     * Begin transaction fires an Events::TRANSACTION_COMMIT event
     */
    public function commit()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(Events::TRANSACTION_COMMIT, $event);
    }

    /**
     * Begin transaction fires an Events::TRANSACTION_ROLLBACK event
     */
    public function rollback()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(Events::TRANSACTION_ROLLBACK, $event);
    }

}