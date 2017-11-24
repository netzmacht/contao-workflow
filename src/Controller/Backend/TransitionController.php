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

namespace Netzmacht\Contao\Workflow\Controller\Backend;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransitController
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
 */
class TransitionController extends AbstractController
{
    /**
     * Execute the transition.
     *
     * @param EntityId $entityId
     * @param string   $transition
     * @param Request  $request
     *
     * @return Response
     */
    public function execute(EntityId $entityId, string $transition, Request $request): Response
    {
        $item    = $this->createItem($entityId);
        $workflow = $this->workflowManager->getWorkflowByItem($item);

        if ($item->getWorkflowName() !== $workflow->getName()) {
            $handler = $this->workflowManager->handle($item, $transition, (bool) $request->query->get('detach'));
        } else {
            $handler = $this->workflowManager->handle($item, $transition);
        }

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
