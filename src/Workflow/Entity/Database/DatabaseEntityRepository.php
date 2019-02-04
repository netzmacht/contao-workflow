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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Database;

use Doctrine\DBAL\Connection;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\Specification;
use function is_array;

/**
 * Class DataEntityRepository
 */
final class DatabaseEntityRepository implements EntityRepository
{
    /**
     * Database connection.
     *
     * @var Connection $connection Database connection.
     */
    private $connection;

    /**
     * Table name.
     *
     * @var string
     */
    private $providerName;

    /**
     * DataEntityRepository constructor.
     *
     * @param Connection $connection
     * @param string     $providerName
     */
    public function __construct(Connection $connection, $providerName)
    {
        $this->connection   = $connection;
        $this->providerName = $providerName;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When an entity could not be found.
     */
    public function find($entityId): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->providerName)
            ->where('id=:id')
            ->setParameter('id', $entityId)
            ->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (is_array($row)) {
            return $row;
        }

        throw new \InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException Not implemented yet.
     */
    public function findBySpecification(Specification $specification): iterable
    {
        throw new \RuntimeException('Not supported.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When an entity could not be found.
     */
    public function add($entity): void
    {
        if (!is_array($entity)) {
            throw UnsupportedEntity::forEntity($entity);
        }

        if ($entity->getId()) {
            $this->connection->update($this->providerName, $entity, ['id' => $entity->getId()]);
        } else {
            $this->connection->insert($this->providerName, $entity);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When an entity could not be found.
     */
    public function remove($entity): void
    {
        if (!is_array($entity)) {
            throw UnsupportedEntity::forEntity($entity);
        }
        
        $this->connection->delete($this->providerName, ['id' => $entity['id']]);
    }
}
