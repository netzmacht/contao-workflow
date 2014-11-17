<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Data;

use Netzmacht\Workflow\Contao\Factory\RepositoryFactory;
use Netzmacht\Workflow\Data\EntityManager as WorkflowEntityManager;
use Netzmacht\Workflow\Transaction\Event\TransactionEvent;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

/**
 * Class EntityManager is the entity manager implementation for Contao.
 *
 * It creates the repositories and handles the transaction as well.
 *
 * @package Netzmacht\Workflow\Contao\Data
 */
class EntityManager implements WorkflowEntityManager, TransactionHandler, EventSubscriber
{
    /**
     * The database connection.
     *
     * @var \Database
     */
    private $connection;

    /**
     * The repository factory.
     *
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * The database connection.
     *
     * @param \Database $connection
     */
    function __construct(\Database $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TransactionEvent::TRANSACTION_BEGIN    => 'begin',
            TransactionEvent::TRANSACTION_COMMIT   => 'commit',
            TransactionEvent::TRANSACTION_ROLLBACK => 'rollback',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($providerName)
    {
        return $this->repositoryFactory->create($providerName);
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $this->connection->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->connection->commitTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        $this->connection->rollbackTransaction();
    }
}
