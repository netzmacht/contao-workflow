<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\RepositoryFactory;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Workflow\Data\EntityRepository;
use Override;
use RuntimeException;

use function class_exists;
use function is_a;

final readonly class ContaoModelEntityRepositoryFactory implements RepositoryFactory
{
    /**
     * @param RepositoryManager                    $repositoryManager Repository manager.
     * @param Adapter<Model>                       $modelAdapter      Model adapter.
     * @param ContaoModelRelatedModelChangeTracker $changeTracker     Related model change tracker.
     */
    public function __construct(
        private RepositoryManager $repositoryManager,
        private Adapter $modelAdapter,
        private ContaoModelRelatedModelChangeTracker $changeTracker,
    ) {
    }

    #[Override]
    public function supports(string $providerName): bool
    {
        $modelClass = $this->modelAdapter->getClassFromTable($providerName);

        return class_exists($modelClass) && is_a($modelClass, Model::class, true);
    }

    /**
     * {@inheritDoc}
     *
     * @throws UnsupportedEntity When Entity type is not supported.
     */
    #[Override]
    public function create(string $providerName): EntityRepository
    {
        try {
            $modelClass = $this->modelAdapter->getClassFromTable($providerName);
        } catch (RuntimeException $exception) {
            throw UnsupportedEntity::withProviderName($providerName, previous: $exception);
        }

        $repository = $this->repositoryManager->getRepository($modelClass);

        return new ContaoModelEntityRepository($repository, $this->changeTracker, $this->repositoryManager);
    }
}
