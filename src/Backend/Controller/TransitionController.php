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

use Netzmacht\Contao\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class TransitController
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
 */
class TransitionController extends AbstractController
{
    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * {@inheritDoc}
     *
     * @param Router $router The router
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        WorkflowTypeRegistry $typeRegistry,
        TemplateEngine $renderer,
        Router $router
    ) {
        parent::__construct($workflowManager, $entityManager, $typeRegistry, $renderer);

        $this->router = $router;
    }

    /**
     * Execute the transition.
     *
     * @param EntityId      $entityId
     * @param string $transition
     *
     * @return Response
     */
    public function execute(EntityId $entityId, string $transition): Response
    {
        $item    = $this->createItem($entityId);
        $handler = $this->workflowManager->handle($item, $transition);

        if ($handler->validate()) {
            $handler->transit();

            return new RedirectResponse(
                $this->router->generate('netzmacht.contao_workflow.backend.step', ['entityId' => (string) $entityId])
            );
        }

        // TODO: Handle invalid transition with required form data.
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
