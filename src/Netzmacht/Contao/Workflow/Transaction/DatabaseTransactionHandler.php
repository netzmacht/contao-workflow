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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DatabaseTransactionHandler implements TransactionHandler, EventSubscriberInterface
{
    /**
     * @var \Contao\Database
     */
    private $database;

    /**
     * @param \Contao\Database $database
     */
    function __construct(\Contao\Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::TRANSACTION_BEGIN  => 'begin',
            Events::TRANSACTION_COMMIT => 'commit',
            Events::TRANSACTION_ROLLBACK => 'rollback',
        );
    }

    /**
     * @{inheritdoc}
     */
    public function begin()
    {
        $this->database->beginTransaction();
    }

    /**
     * @{inheritdoc}
     */
    public function commit()
    {
        $this->database->commitTransaction();
    }

    /**
     * @{inheritdoc}
     */
    public function rollback()
    {
        $this->database->rollbackTransaction();
    }

} 