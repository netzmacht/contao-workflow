<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Netzmacht\Workflow\Data\EntityId;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StepController
 *
 * The step controller handles the view of a step.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Backend\Controller
 */
final class StepController extends AbstractController
{
    /**
     * Execute the controller.
     *
     * @param EntityId $entityId The entity id.
     * @param string   $module   Module name.
     *
     * @return Response
     */
    public function __invoke(EntityId $entityId, string $module): Response
    {
        $item        = $this->createItem($entityId);
        $workflow    = $this->workflowManager->getWorkflowByItem($item);
        $currentStep = null;

        if (!$item->isWorkflowStarted()) {
            return new RedirectResponse(
                $this->router->generate(
                    'netzmacht.contao_workflow.backend.transition',
                    [
                        'module'     => $module,
                        'entityId'   => (string) $entityId,
                        'transition' => $workflow->getStartTransition()->getName(),
                    ]
                )
            );
        }

        if ($workflow->getName() !== $item->getWorkflowName()) {
            return new RedirectResponse(
                $this->router->generate(
                    'netzmacht.contao_workflow.backend.transition',
                    [
                        'module'     => $module,
                        'entityId'   => (string) $entityId,
                        'transition' => $workflow->getStartTransition()->getName(),
                        'detach'     => true,
                    ]
                )
            );
        }

        $currentStep = $workflow->getStep($item->getCurrentStepName());
        $view        = $this->viewFactory->create($item, $currentStep, ['module' => $module]);

        return $view->render();
    }
}
