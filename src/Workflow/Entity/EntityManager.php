<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity;

use Doctrine\DBAL\Connection;
use Netzmacht\Workflow\Data\EntityManager as WorkflowEntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class EntityManager is the entity manager implementation for Contao.
 *
 * It creates the repositories and handles the transaction as well.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Data
 */
class EntityManager implements WorkflowEntityManager, TransactionHandler
{
    /**
     * Entity repositories.
     *
     * @var EntityRepository[]|array
     */
    private $repositories = [];

    /**
     * Repository manager.
     *
     * @var RepositoryFactory
     */
    private $repositoryFactory;
    
    /**
     * @var Connection
     */
    private $connection;

    /**
     * The database connection.
     *
     * @param RepositoryFactory $repositoryFactory
     * @param Connection        $connection
     */
    public function __construct(RepositoryFactory $repositoryFactory, Connection $connection)
    {
        $this->repositoryFactory = $repositoryFactory;
        $this->connection        = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $providerName): EntityRepository
    {
        if (isset($this->repositories[$providerName])) {
            return $this->repositories[$providerName];
        }

        $this->repositories[$providerName] = $this->repositoryFactory->create($providerName);

        return $this->repositories[$providerName];
    }

    /**
     * {@inheritdoc}
     */
    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(): void
    {
        $this->connection->rollBack();
    }
}
