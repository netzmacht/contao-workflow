<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Database;

use Doctrine\DBAL\Connection;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\RepositoryFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;
use Override;

final class DatabaseEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * Database connection.
     */
    private Connection $connection;

    /** @param Connection $connection Database connection. */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    #[Override]
    public function supports(string $providerName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$providerName]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    #[Override]
    public function create(string $providerName): EntityRepository
    {
        if (! $this->supports($providerName)) {
            throw UnsupportedEntity::withProviderName($providerName);
        }

        return new DatabaseEntityRepository($this->connection, $providerName);
    }
}
