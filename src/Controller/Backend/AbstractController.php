<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface as Router;

use function sprintf;

abstract class AbstractController
{
    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    protected $workflowManager;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The router.
     *
     * @var Router
     */
    protected $router;

    /**
     * View factory.
     *
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @param WorkflowManager $workflowManager Workflow manager.
     * @param EntityManager   $entityManager   Entity manager.
     * @param ViewFactory     $viewFactory     View factory.
     * @param Router          $router          Router.
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        ViewFactory $viewFactory,
        Router $router
    ) {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
        $this->viewFactory     = $viewFactory;
        $this->router          = $router;
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
