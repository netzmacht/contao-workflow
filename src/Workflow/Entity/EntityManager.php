<?php

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
 */
final class EntityManager implements WorkflowEntityManager, TransactionHandler
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
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The database connection.
     *
     * @param RepositoryFactory $repositoryFactory Repository factory.
     * @param Connection        $connection        Database connection.
     */
    public function __construct(RepositoryFactory $repositoryFactory, Connection $connection)
    {
        $this->repositoryFactory = $repositoryFactory;
        $this->connection        = $connection;
    }

    public function getRepository(string $providerName): EntityRepository
    {
        if (isset($this->repositories[$providerName])) {
            return $this->repositories[$providerName];
        }

        $this->repositories[$providerName] = $this->repositoryFactory->create($providerName);

        return $this->repositories[$providerName];
    }

    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }
}
