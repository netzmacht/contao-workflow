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

namespace Netzmacht\Contao\Workflow\Entity\Database;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Workflow\Entity\RepositoryFactory;
use Netzmacht\Contao\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

/**
 * Class DataEntityRepositoryFactory
 *
 * @package Netzmacht\Contao\Workflow\Entity\Data
 */
class DatabaseEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * DatabaseEntityRepositoryFactory constructor.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
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
        if (!$this->supports($providerName)) {
            throw UnsupportedEntity::withProviderName($providerName);
        }
        
        return new DatabaseEntityRepository($this->connection, $providerName);
    }
}
