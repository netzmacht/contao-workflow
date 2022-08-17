<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Database;

use Doctrine\DBAL\Connection;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\RepositoryFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

final class DatabaseEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function supports(string $providerName): bool
    {
        return $this->connection->getSchemaManager()->tablesExist([$providerName]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    public function create(string $providerName): EntityRepository
    {
        if (! $this->supports($providerName)) {
            throw UnsupportedEntity::withProviderName($providerName);
        }

        return new DatabaseEntityRepository($this->connection, $providerName);
    }
}
