<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Data;

use Netzmacht\Contao\Workflow\Data\RepositoryFactory;
use Netzmacht\Workflow\Data\EntityManager as WorkflowEntityManager;
use Netzmacht\Workflow\Transaction\Event\TransactionEvent;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as EventSubscriber;

/**
 * Class EntityManager is the entity manager implementation for Contao.
 *
 * It creates the repositories and handles the transaction as well.
 *
 * @package Netzmacht\Contao\Workflow\Data
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
     * @param \Database         $connection        The database connection.
     * @param RepositoryFactory $repositoryFactory The repositroy factory.
     */
    public function __construct(\Database $connection, RepositoryFactory $repositoryFactory)
    {
        $this->connection        = $connection;
        $this->repositoryFactory = $repositoryFactory;
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
