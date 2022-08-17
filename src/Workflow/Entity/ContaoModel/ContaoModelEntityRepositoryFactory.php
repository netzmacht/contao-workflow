<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\RepositoryFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;

use function class_exists;
use function is_a;

final class ContaoModelEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * Contao model repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Model adapter.
     *
     * @var Adapter|Model
     */
    private $modelAdapter;

    /**
     * Related changes.
     *
     * @var ContaoModelRelatedModelChangeTracker
     */
    private $changeTracker;

    /**
     * @param RepositoryManager                    $repositoryManager Repository manager.
     * @param Adapter|Model                        $modelAdapter      Model adapter.
     * @param ContaoModelRelatedModelChangeTracker $changeTracker     Related model change tracker.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        $modelAdapter,
        ContaoModelRelatedModelChangeTracker $changeTracker
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->modelAdapter      = $modelAdapter;
        $this->changeTracker     = $changeTracker;
    }

    public function supports(string $providerName): bool
    {
        $modelClass = $this->modelAdapter->getClassFromTable($providerName);

        return $modelClass && class_exists($modelClass) && is_a($modelClass, Model::class, true);
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    public function create(string $providerName): EntityRepository
    {
        $modelClass = $this->modelAdapter->getClassFromTable($providerName);
        if (! $modelClass) {
            throw UnsupportedEntity::withProviderName($providerName);
        }

        $repository = $this->repositoryManager->getRepository($modelClass);

        return new ContaoModelEntityRepository($repository, $this->changeTracker, $this->repositoryManager);
    }
}
