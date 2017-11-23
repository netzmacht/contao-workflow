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

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StepController
 *
 * The step controller handles the view of a step.
 *
 * @package Netzmacht\Contao\Workflow\Backend\Controller
 */
class StepController extends AbstractController
{
    /**
     * Execute the controller.
     *
     * @param EntityId $entityId The enetity id.
     *
     * @return Response
     */
    public function execute(EntityId $entityId): Response
    {
        $item        = $this->createItem($entityId);
        $workflow    = $this->workflowManager->getWorkflowByItem($item);
        $renderer    = $this->getWorkflowType($workflow)->getRenderer();
        $currentStep = null;
        $errors      = [];

        if ($workflow->getName() !== $item->getWorkflowName()) {
            return new RedirectResponse(
                $this->router->generate(
                    'netzmacht.contao_workflow.backend.transition',
                    [
                        'entityId'   => (string) $entityId,
                        'transition' => $workflow->getStartTransition()->getName(),
                        'detach'     => true,
                    ]
                )
            );
        } elseif ($item->isWorkflowStarted()) {
            $currentStep = $workflow->getStep($item->getCurrentStepName());
        }

        return $this->renderer->renderResponse(
            '@NetzmachtContaoWorkflow/backend/step.html.twig',
            [
                'headline'           => $this->generateHeadline($workflow, $currentStep),
                'subheadline'        => $renderer->renderLabel($item),
                'errors'             => $errors,
                'item'               => $item,
                'transitions'        => $this->getAvailableTransitions($workflow, $item),
                'hasWorkflowChanged' => $workflow->getName() !== $item->getWorkflowName(),
                'workflow'           => $workflow,
                'currentStep'        => $currentStep,
                'view'               => $renderer->renderDefaultView($item),
            ]
        );
    }

    /**
     * Generate the headline.
     *
     * @param Workflow  $workflow    Workflow of the item.
     * @param Step|null $currentStep Current step.
     *
     * @return string
     */
    private function generateHeadline(Workflow $workflow, ?Step $currentStep): string
    {
        $headline  = $workflow->getLabel();

        if ($currentStep) {
            $headline .= ' &raquo; ' . $currentStep->getLabel();
        }

        return $headline;
    }

    /**
     * Get all available transitions.
     *
     * @param Workflow $workflow Workflow.
     * @param Item     $item     The item.
     *
     * @return Transition[]|array
     */
    private function getAvailableTransitions(Workflow $workflow, Item $item): array
    {
        $transitions = array();

        if (!$item->isWorkflowStarted() || $workflow->getName() || $item->getWorkflowName()) {
            $transitions[] = $workflow->getStartTransition();
        } else {
            $step = $workflow->getStep($item->getCurrentStepName());

            foreach ($step->getAllowedTransitions() as $transitionName) {
                $transitions[] = $workflow->getTransition($transitionName);
            }
        }

        return $transitions;
    }
}
