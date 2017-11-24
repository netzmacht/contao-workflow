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

use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
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
                'history'            => $this->generateStateHistory($item),
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

    /**
     * Generate the state history by translating transitions and states with label values.
     *
     * @param Item $item The item
     *
     * @return array
     */
    private function generateStateHistory(Item $item): array
    {
        $history = [];

        foreach ($item->getStateHistory() as $state) {
            $data = [
                'workflowName'   => $state->getWorkflowName(),
                'transitionName' => $state->getTransitionName(),
                'stepName'       => $state->getStepName(),
                'successful'     => $state->isSuccessful(),
                'reachedAt'      => $state->getReachedAt(),
                'user'           => '-',
                'scope'          => '-',
            ];

            $stateData = $state->getData();

            if (isset($stateData['default']['metadata']['userId'])) {
                $userId = EntityId::fromString($stateData['default']['metadata']['userId']);
                $repository = $this->entityManager->getRepository($userId->getProviderName());
                $user       = $repository->find($userId->getIdentifier());

                if ($user instanceof Entity) {
                    if ($user->getProviderName() === 'tl_user') {
                        $data['user'] = $user->getProperty('name');
                    } elseif ($user->getProviderName() === 'tl_member') {
                        $data['user'] = $user->getProperty('firstname') . ' ' . $user->getProperty('lastname');
                    }

                    $data['user'] .= sprintf(
                        ' <span class="tl_gray">[%s]</span>',
                        $user->getProperty('username') ?: $user->getId()
                    );
                }
            }

            if (isset($stateData['default']['metadata']['scope'])) {
                $data['scope'] = $stateData['default']['metadata']['scope'];
            }

            try {
                $workflow = $this->workflowManager->getWorkflowByName($state->getWorkflowName());
                $data['workflowName'] = $workflow->getLabel();

                if ($workflow->hasTransition($state->getTransitionName())) {
                    $data['transitionName'] = $workflow->getTransition($state->getTransitionName())->getLabel();
                }

                try {
                    $data['stepName'] = $workflow->getStep($state->getStepName())->getLabel();
                } catch (StepNotFoundException $e) {
                    //
                }
            } catch(WorkflowNotFound $e) {
                //
            }

            array_unshift($history, $data);
        }

        return $history;
    }
}
