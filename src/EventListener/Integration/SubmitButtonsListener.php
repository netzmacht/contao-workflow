<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Integration;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Input;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\EntityManager;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Routing\RouterInterface as Router;

use function sprintf;

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
     * Input adapter.
     *
     * @var Adapter|Input
     */
    private $inputAdapter;

    /**
     * @param WorkflowManager $workflowManager Workflow manager.
     * @param EntityManager   $entityManager   Entity manager.
     * @param Router          $router          Router.
     * @param Adapter|Input   $inputAdapter    Input adapter.
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        Router $router,
        $inputAdapter
    ) {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
        $this->router          = $router;
        $this->inputAdapter    = $inputAdapter;
    }

    /**
     * Add transitions as submit buttons listening to the buttons_callback.
     *
     * @param array<string,string> $buttons       Current buttons.
     * @param DataContainer        $dataContainer Data container driver.
     *
     * @return array<string,string>
     */
    public function addTransitionButtons(array $buttons, DataContainer $dataContainer): array
    {
        if (! $dataContainer->activeRecord) {
            return $buttons;
        }

        $entityId   = EntityId::fromProviderNameAndId($dataContainer->table, (int) $dataContainer->id);
        $repository = $this->entityManager->getRepository($dataContainer->table);
        $entity     = $repository->find($entityId->getIdentifier());

        if (! $this->workflowManager->hasWorkflow($entityId, $entity)) {
            return $buttons;
        }

        $workflow = $this->workflowManager->getWorkflow($entityId, $entity);
        $item     = $this->workflowManager->createItem($entityId, $entity);

        foreach ($workflow->getAvailableTransitions($item) as $transition) {
            if ($transition->getConfigValue('hide')) {
                continue;
            }

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
     * @throws RedirectResponseException When a workflow exists and a transition was triggered.
     */
    public function redirectToTransition(DataContainer $dataContainer): void
    {
        $transition = Input::post('workflowTransition');
        if (! $transition) {
            return;
        }

        $entityId   = EntityId::fromProviderNameAndId($dataContainer->table, (int) $dataContainer->id);
        $repository = $this->entityManager->getRepository($dataContainer->table);
        $entity     = $repository->find($entityId->getIdentifier());

        if (! $this->workflowManager->hasWorkflow($entityId, $entity)) {
            return;
        }

        $workflow = $this->workflowManager->getWorkflow($entityId, $entity);
        $route    = $this->router->generate(
            'netzmacht.contao_workflow.backend.transition',
            [
                'entityId'   => $entityId,
                'transition' => $transition,
                'module'     => $this->inputAdapter->get('do'),
                'detach'     => $dataContainer->activeRecord
                    && $workflow->getName() !== $dataContainer->activeRecord->workflow ? '1' : '',
            ]
        );

        throw new RedirectResponseException($route, 307);
    }

    /**
     * Genearte a transition submit button.
     *
     * @param string     $buttonName The button name used as css id.
     * @param Transition $transition The transition.
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
