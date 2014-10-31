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

use Netzmacht\Contao\Workflow\Event\TransactionEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DatabaseTransactionHandler subscribes to the event based transaction handler.
 *
 * It provides transaction handling for the contao database connection.
 *
 * @package Netzmacht\Contao\Workflow\Transaction
 */
class DatabaseTransactionHandler implements TransactionHandler, EventSubscriberInterface
{
    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Construct.
     *
     * @param \Database $database The database connection.
     */
    public function __construct(\Database $database)
    {
        $this->database = $database;
    }

    /**
     * Get the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            TransactionEvents::TRANSACTION_BEGIN  => 'begin',
            TransactionEvents::TRANSACTION_COMMIT => 'commit',
            TransactionEvents::TRANSACTION_ROLLBACK => 'rollback',
        );
    }

    /**
     * Begin a transaction.
     *
     * @return void
     */
    public function begin()
    {
        $this->database->beginTransaction();
    }

    /**
     * Commit changes.
     *
     * @return void
     */
    public function commit()
    {
        $this->database->commitTransaction();
    }

    /**
     * Rollback changes.
     *
     * @return void
     */
    public function rollback()
    {
        $this->database->rollbackTransaction();
    }
}
