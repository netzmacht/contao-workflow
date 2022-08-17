<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Contao\Model;
use Contao\Model\Collection;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Workflow\Data\EntityRepository as WorkflowEntityRepository;
use Netzmacht\Workflow\Data\Specification;

use function array_filter;
use function assert;
use function get_class;
use function is_array;
use function sprintf;

/**
 * Class EntityRepository stores an entity.
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
     * Related model change tracker.
     *
     * @var ContaoModelRelatedModelChangeTracker
     */
    private $changeTracker;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Construct.
     *
     * @param Repository                           $repository        Contao model repository.
     * @param ContaoModelRelatedModelChangeTracker $changeTracker     Related model change tracker.
     * @param RepositoryManager                    $repositoryManager The repository manager.
     */
    public function __construct(
        Repository $repository,
        ContaoModelRelatedModelChangeTracker $changeTracker,
        RepositoryManager $repositoryManager
    ) {
        $this->repository        = $repository;
        $this->changeTracker     = $changeTracker;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Add an entity to the repository.
     *
     * @param Model|mixed $entity The new entity.
     *
     * @throws AssertionFailedException If an invalid entity type is given.
     */
    public function add($entity): void
    {
        Assertion::isInstanceOf(
            $entity,
            Model::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        $this->repository->save($entity);

        foreach ($this->changeTracker->release($entity) as $model) {
            /** @psalm-var class-string<Model> $modelClass */
            $modelClass = get_class($model);
            $repository = $this->repositoryManager->getRepository($modelClass);
            $repository->save($model);
        }
    }

    /**
     * Find an entity by id.
     *
     * @param int|mixed $entityId The Entity id.
     *
     * @throws InvalidArgumentException If no entity were found.
     */
    public function find($entityId): Model
    {
        $model = $this->repository->find((int) $entityId);

        if (! $model) {
            throw new InvalidArgumentException(sprintf('Could not find entity "%s"', $entityId));
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
        if ($specification instanceof ContaoModelSpecificationAwareSpecification) {
            $models   = $this->repository->findBySpecification($specification->asModelSpecification()) ?: [];
            $entities = [];

            assert($models instanceof Collection || is_array($models));
            foreach ($models as $model) {
                $entities[] = $model;
            }

            return $entities;
        }

        $models   = $this->repository->findAll() ?: [];
        $entities = [];

        assert($models instanceof Collection || is_array($models));
        foreach ($models as $model) {
            $entities[] = $model;
        }

        return array_filter(
            $entities,
            // @codingStandardsIgnoreStart
            static function (Model $entity) use ($specification) {
                return $specification->isSatisfiedBy($entity);
            }
            // @codingStandardsIgnoreEnd
        );
    }

    /**
     * Remove an entity.
     *
     * {@inheritdoc}
     *
     * @throws AssertionFailedException If an invalid entity type is given.
     */
    public function remove($entity): void
    {
        Assertion::isInstanceOf(
            $entity,
            Model::class,
            'Entity repository requires an instance of the ContaoModelEntity'
        );

        $this->repository->delete($entity);
    }
}
