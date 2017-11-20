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

namespace Netzmacht\Contao\Workflow\Entity;

use Assert\Assertion;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Workflow\Data\EntityRepository as WorkflowEntityRepository;
use Netzmacht\Workflow\Data\Specification;

/**
 * Class EntityRepository stores an entity.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
class EntityRepository implements WorkflowEntityRepository
{
    /**
     * Contao model repository.
     *
     * @var Repository
     */
    private $repository;

    /**
     * Construct.
     *
     * @param Repository $repository Contao model repository.
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Add an entity to the repository.
     *
     * @param Entity $entity The new entity.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If an invalid entity type is given.
     */
    public function add($entity)
    {
        Assertion::isInstanceOf(
            $entity,
            ContaoModelEntity::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        /** @var ContaoModelEntity $entity */
        $this->repository->save($entity->getModel());
    }

    /**
     * Find an entity by id.
     *
     * @param int $entityId The Entity id.
     *
     * @return Entity
     *
     * @throws \InvalidArgumentException If no entity were found.
     */
    public function find($entityId)
    {
        $model = $this->repository->find((int) $entityId);

        if (!$model) {
            throw new \InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
        }

        return new ContaoModelEntity($model);
    }

    /**
     * Find by a specification.
     *
     * It is highly recommend to pass a QuerySpecification. Otherwise all items have to be loaded!.
     *
     * @param Specification $specification The specification.
     *
     * @return array|Entity|\string[]
     */
    public function findBySpecification(Specification $specification)
    {
        // Todo: Implement.

        throw new \RuntimeException('Not implemented yet.');
    }

    /**
     * Remove an entity.
     *
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If an invalid entity type is given.
     */
    public function remove($entity)
    {
        Assertion::isInstanceOf(
            $entity,
            ContaoModelEntity::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        /** @var ContaoModelEntity $entity */
        $this->repository->delete($entity->getModel());
    }
}
