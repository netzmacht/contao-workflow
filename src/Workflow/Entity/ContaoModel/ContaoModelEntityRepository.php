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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Assert\Assertion;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Contao\Toolkit\Data\Model\Specification as ModelSpecification;
use Netzmacht\Workflow\Data\EntityRepository as WorkflowEntityRepository;
use Netzmacht\Workflow\Data\Specification;

/**
 * Class EntityRepository stores an entity.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Entity
 */
final class ContaoModelEntityRepository implements WorkflowEntityRepository
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
     * @param Model $entity The new entity.
     *
     * @return void
     *
     * @throws \Assert\AssertionFailedException If an invalid entity type is given.
     */
    public function add($entity): void
    {
        Assertion::isInstanceOf(
            $entity,
            Model::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        /** @var Model $entity */
        $this->repository->save($entity);
    }

    /**
     * Find an entity by id.
     *
     * @param int $entityId The Entity id.
     *
     * @return Model
     *
     * @throws \InvalidArgumentException If no entity were found.
     */
    public function find($entityId): Model
    {
        $model = $this->repository->find((int) $entityId);

        if (!$model) {
            throw new \InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
        }

        return $model;
    }

    /**
     * Find by a specification.
     *
     * It is highly recommend to pass a QuerySpecification. Otherwise all items have to be loaded!.
     *
     * @param Specification $specification The specification.
     *
     * @return Model[]|iterable
     */
    public function findBySpecification(Specification $specification): iterable
    {
        if ($specification instanceof ModelSpecification) {
            $models   = $this->repository->findBySpecification($specification) ?: [];
            $entities = [];

            foreach ($models as $model) {
                $entities[] = $model;
            }

            return $entities;
        }

        $models   = $this->repository->findAll() ?: [];
        $entities = [];

        foreach ($models as $model) {
            $entities[] = $model;
        }

        return array_filter(
            $entities,
            function (Model $entity) use ($specification) {
                return $specification->isSatisfiedBy($entity);
            }
        );
    }

    /**
     * Remove an entity.
     *
     * {@inheritdoc}
     *
     * @throws \Assert\AssertionFailedException If an invalid entity type is given.
     */
    public function remove($entity): void
    {
        Assertion::isInstanceOf(
            $entity,
            Model::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        /** @var Model $entity */
        $this->repository->delete($entity);
    }
}
