<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Contao\CoreBundle\Controller\AbstractBackendController;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface as Router;

use function sprintf;

abstract class AbstractController extends AbstractBackendController
{
    /**
     * @param WorkflowManager $workflowManager Workflow manager.
     * @param EntityManager   $entityManager   Entity manager.
     * @param ViewFactory     $viewFactory     View factory.
     * @param Router          $router          Router.
     */
    public function __construct(
        protected WorkflowManager $workflowManager,
        protected EntityManager $entityManager,
        protected ViewFactory $viewFactory,
        protected Router $router,
    ) {
    }

    /**
     * Find the entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @throws NotFoundHttpException If entity is not found.
     */
    protected function createItem(EntityId $entityId): Item
    {
        try {
            $repository = $this->entityManager->getRepository($entityId->getProviderName());
            $entity     = $repository->find($entityId->getIdentifier());
        } catch (UnsupportedEntity $e) {
            throw new NotFoundHttpException(sprintf('Entity "%s" not found.', (string) $entityId), $e);
        }

        return $this->workflowManager->createItem($entityId, $entity);
    }
}
