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

namespace Netzmacht\Contao\Workflow\Backend\Controller;

use Netzmacht\Contao\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Contao\Workflow\Type\WorkflowType;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class AbstractController
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
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
     * Template engine.
     *
     * @var TemplateEngine
     */
    protected $renderer;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The workflow type registry.
     *
     * @var WorkflowTypeRegistry
     */
    protected $typeRegistry;

    /**
     * The router.
     *
     * @var Router
     */
    protected $router;

    /**
     * AbstractController constructor.
     *
     * @param WorkflowManager      $workflowManager
     * @param EntityManager        $entityManager
     * @param WorkflowTypeRegistry $typeRegistry
     * @param TemplateEngine       $renderer
     * @param Router               $router
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        WorkflowTypeRegistry $typeRegistry,
        TemplateEngine $renderer,
        Router $router
    ) {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
        $this->typeRegistry    = $typeRegistry;
        $this->renderer        = $renderer;
        $this->router          = $router;
    }

    /**
     * Find the entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return Item
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

    /**
     * Get the workflow type.
     *
     * @param Workflow $workflow The workflow type.
     *
     * @return WorkflowType
     */
    protected function getWorkflowType(Workflow $workflow): WorkflowType
    {
        return $this->typeRegistry->getType($workflow->getConfigValue('type'));
    }
}
