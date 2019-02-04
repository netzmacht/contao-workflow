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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Integration;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use Contao\Input;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\EntityManager;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class SubmitButtonsListener
 */
final class SubmitButtonsListener
{
    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Router.
     *
     * @var Router
     */
    private $router;

    /**
     * SubmitButtonsListener constructor.
     *
     * @param WorkflowManager $workflowManager Workflow manager.
     * @param EntityManager   $entityManager   Entity manager.
     * @param Router          $router          Router.
     */
    public function __construct(WorkflowManager $workflowManager, EntityManager $entityManager, Router $router)
    {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
        $this->router          = $router;
    }

    /**
     * Add transitions as submit buttons listening to the buttons_callback.
     *
     * @param array         $buttons       Current buttons.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function addTransitionButtons(array $buttons, DataContainer $dataContainer): array
    {
        if (!$dataContainer->activeRecord) {
            return $buttons;
        }

        $entityId   = EntityId::fromProviderNameAndId($dataContainer->table, (int) $dataContainer->id);
        $repository = $this->entityManager->getRepository($dataContainer->table);
        $entity     = $repository->find($entityId->getIdentifier());

        if (!$this->workflowManager->hasWorkflow($entityId, $entity)) {
            return $buttons;
        }

        $workflow = $this->workflowManager->getWorkflow($entityId, $entity);
        $item     = $this->workflowManager->createItem($entityId, $entity);

        foreach ($workflow->getAvailableTransitions($item) as $transition) {
            $name           = 'saveNtransition_' . $transition->getName();
            $buttons[$name] = $this->generateTransitionButton($name, $transition);
        }

        return $buttons;
    }

    /**
     * Handle the onsubmit_callback and redirect to a workflow transition view if transition button was used.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return void
     *
     * @throws RedirectResponseException When a workflow exists and a transition was triggered.
     */
    public function redirectToTransition(DataContainer $dataContainer): void
    {
        $transition = Input::post('workflowTransition');
        if (!$transition) {
            return;
        }

        $entityId   = EntityId::fromProviderNameAndId($dataContainer->table, (int) $dataContainer->id);
        $repository = $this->entityManager->getRepository($dataContainer->table);
        $entity     = $repository->find($entityId->getIdentifier());

        if (!$this->workflowManager->hasWorkflow($entityId, $entity)) {
            return;
        }

        $workflow = $this->workflowManager->getWorkflow($entityId, $entity);
        $route    = $this->router->generate(
            'netzmacht.contao_workflow.backend.transition',
            [
                'entityId'   => $entityId,
                'transition' => $transition,
                'detach'     => $workflow->getName() !== $dataContainer->activeRecord->workflow,
            ]
        );

        throw new RedirectResponseException($route);
    }

    /**
     * Genearte a transition submit button.
     *
     * @param string     $buttonName The button name used as css id.
     * @param Transition $transition The transition.
     *
     * @return string
     */
    private function generateTransitionButton(string $buttonName, Transition $transition): string
    {
        return sprintf(
            '<button type="submit" name="workflowTransition" id="%s" value="%s" class="tl_submit">%s</button>',
            $buttonName,
            $transition->getName(),
            $transition->getLabel()
        );
    }
}
