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

use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * Authorization checker.
     *
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AbstractController constructor.
     *
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
     * @return Response
     *
     * @throws AccessDeniedHttpException When no access is granted.
     */
    public function __invoke(EntityId $entityId, string $module): Response
    {
        $item        = $this->createItem($entityId);
        $workflow    = $this->workflowManager->getWorkflowByItem($item);
        $currentStep = null;

        if (!$item->isWorkflowStarted()) {
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

        $currentStep = $workflow->getStep($item->getCurrentStepName());
        if (! $this->authorizationChecker->isGranted($currentStep, $item)) {
            throw new AccessDeniedHttpException();
        }

        $view = $this->viewFactory->create($item, $currentStep, ['module' => $module]);

        return $view->render();
    }
}
