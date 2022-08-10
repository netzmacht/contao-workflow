<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Netzmacht\ContaoWorkflowBundle\Security\WorkflowPermissions;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * The step controller handles the view of a step.
 */
final class StepController extends AbstractController
{
    /**
     * Authorization checker.
     *
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param WorkflowManager               $workflowManager      Workflow manager.
     * @param EntityManager                 $entityManager        Entity manager.
     * @param ViewFactory                   $viewFactory          View factory.
     * @param Router                        $router               Router.
     * @param AuthorizationCheckerInterface $authorizationChecker Authorization checker.
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        ViewFactory $viewFactory,
        Router $router,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($workflowManager, $entityManager, $viewFactory, $router);

        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Execute the controller.
     *
     * @param EntityId $entityId The entity id.
     * @param string   $module   Module name.
     *
     * @throws AccessDeniedHttpException When no access is granted.
     */
    public function __invoke(EntityId $entityId, string $module): Response
    {
        $item        = $this->createItem($entityId);
        $workflow    = $this->workflowManager->getWorkflowByItem($item);
        $currentStep = null;

        if (! $item->isWorkflowStarted()) {
            $view = $this->viewFactory->create($item, $currentStep, ['module' => $module]);

            return $view->render();
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

        $stepName = $item->getCurrentStepName();
        if ($stepName === null) {
            throw new NotFoundHttpException();
        }

        $currentStep = $workflow->getStep($stepName);
        if (! $this->authorizationChecker->isGranted(WorkflowPermissions::accessStep($currentStep), $item)) {
            throw new AccessDeniedHttpException();
        }

        $view = $this->viewFactory->create($item, $currentStep, ['module' => $module]);

        return $view->render();
    }
}
