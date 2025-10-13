<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Database;

use ArrayObject;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\Specification;
use Override;
use RuntimeException;

use function is_array;
use function sprintf;

final class DatabaseEntityRepository implements EntityRepository
{
    /**
     * Database connection.
     *
     * @var Connection $connection Database connection.
     */
    private Connection $connection;

    /**
     * Table name.
     */
    private string $providerName;

    public function __construct(Connection $connection, string $providerName)
    {
        $this->connection   = $connection;
        $this->providerName = $providerName;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException When an entity could not be found.
     */
    #[Override]
    public function find(mixed $entityId): ArrayObject
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->providerName)
            ->where('id=:id')
            ->setParameter('id', $entityId)
            ->executeQuery();

        if ($result->rowCount() !== 1) {
            throw new InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
        }

        $row = $result->fetchAssociative();
        if (is_array($row)) {
            return new ArrayObject($row);
        }

        throw new InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
    }

    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException Not implemented yet.
     */
    #[Override]
    public function findBySpecification(Specification $specification): iterable
    {
        throw new RuntimeException('Not supported.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When an entity could not be found.
     */
    #[Override]
    public function add(mixed $entity): void
    {
        if (! $entity instanceof ArrayObject) {
            throw UnsupportedEntity::forEntity($entity);
        }

        if (isset($entity['id'])) {
            $this->connection->update($this->providerName, $entity->getArrayCopy(), ['id' => $entity['id']]);
        } else {
            $this->connection->insert($this->providerName, $entity->getArrayCopy());
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When an entity could not be found.
     */
    #[Override]
    public function remove(mixed $entity): void
    {
        if (! $entity instanceof ArrayObject) {
            throw UnsupportedEntity::forEntity($entity);
        }

        $this->connection->delete($this->providerName, ['id' => $entity['id']]);
    }
}
