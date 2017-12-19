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

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class AbstractController
 *
 * @package Netzmacht\ContaoWorkflowBundle\Backend\Controller
 */
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
     * AbstractController constructor.
     *
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
     * @return Item
     *
     * @throws NotFoundHttpException If entity is not found.
     */
    protected function createItem(EntityId $entityId): Item
    {
        try {
            $repository = $this->entityManager->getRepository($entityId->getProviderName());
            $entity     = $repository->find($entityId->getIdentifier());
        } catch (UnsupportedEntity $e) {
            throw new NotFoundHttpException('Entity not found.', $e);
        }

        return $this->workflowManager->createItem($entityId, $entity);
    }
}
