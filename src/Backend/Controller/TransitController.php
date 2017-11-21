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

use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Exception\UnsupportedEntity;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeProvider;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TransitController
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
 */
class TransitController
{
    /**
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * @var TemplateEngine
     */
    private $renderer;

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var WorkflowTypeProvider
     */
    private $typeProvider;

    /**
     * TransitController constructor.
     *
     * @param WorkflowManager      $workflowManager
     * @param EntityManager        $entityManager
     * @param WorkflowTypeProvider $typeProvider
     * @param TemplateEngine       $renderer
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        WorkflowTypeProvider $typeProvider,
        TemplateEngine $renderer
    ) {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
        $this->typeProvider    = $typeProvider;
        $this->renderer        = $renderer;
    }

    /**
     * @param string      $entityId
     * @param null|string $transition
     *
     * @return Response
     */
    public function execute(string $entityId, ?string $transition = null): Response
    {
        $entityId = EntityId::fromString($entityId);
        $item     = $this->createItem($entityId);

        $workflow     = $this->workflowManager->getWorkflowByName($item->getWorkflowName());
        $workflowType = $this->typeProvider->getType($workflow->getConfigValue('type'));

        return $this->renderer->renderResponse(
            '@NetzmachtContaoWorkflow/backend/transit.html.twig',
            [
                'handler'       => $handler,
                'headline'      => $this->generateHeadline($handler),
                'item'          => $workflowType->getRenderer()->renderDefaultView($item),
                'errors'        => null,
            ]
        );
    }

    /**
     * Find the entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return Item
     */
    private function createItem(EntityId $entityId): Item
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
     * Generate the headline.
     *
     * @param TransitionHandler $handler The transition handler.
     *
     * @return string
     */
    private function generateHeadline(TransitionHandler $handler): string
    {
        $headline = $handler->getWorkflow()->getLabel();

        if ($handler->getCurrentStep()) {
            $headline .= ' &raquo; ' . $handler->getCurrentStep()->getLabel();
        }

        $headline .= ' &raquo; ' . $handler->getTransition()->getLabel();

        return $headline;
    }
}
