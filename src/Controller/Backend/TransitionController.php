<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Controller\Backend;

use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;
use Netzmacht\ContaoWorkflowBundle\Form\TransitionFormType;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface as Router;

use function sprintf;
use function urldecode;

final class TransitionController extends AbstractController
{
    /**
     * The form factory.
     *
     * @var FormFactory
     */
    private $formFactory;

    /**
     * {@inheritDoc}
     *
     * @param FormFactory $formFactory The form factory.
     */
    public function __construct(
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        ViewFactory $viewFactory,
        Router $router,
        FormFactory $formFactory
    ) {
        parent::__construct($workflowManager, $entityManager, $viewFactory, $router);

        $this->formFactory = $formFactory;
    }

    // phpcs:disable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectAnnotationsGroup
    /**
     * Execute the transition.
     *
     * @param EntityId $entityId   EntityId of current entity.
     * @param string   $transition The transition name.
     * @param string   $module     The module name.
     * @param Request  $request    The web request.
     *
     * @psalm-suppress InvalidThrow - Needs to be fixed upstream
     * @throws WorkflowException When the workflow handling fails.
     */
    // phpcs:enable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectAnnotationsGroup
    public function __invoke(EntityId $entityId, string $transition, string $module, Request $request): Response
    {
        $item      = $this->createItem($entityId);
        $workflow  = $this->workflowManager->getWorkflowByItem($item);
        $handler   = $this->createTransitionHandler($entityId, $transition, $request, $item, $workflow);
        $payload   = [];
        $validForm = true;
        $form      = null;

        if ($handler->getRequiredPayloadProperties()) {
            $form = $this->formFactory->create(
                TransitionFormType::class,
                [],
                ['handler' => $handler, 'item' => $item]
            );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $payload = $form->getData();
            } else {
                $validForm = false;
            }
        }

        if ($validForm && $handler->validate($payload)) {
            $state = $handler->transit();

            if ($state->isSuccessful()) {
                return new RedirectResponse($this->getRedirectUrl($entityId, $request));
            }
        }

        $transition = $workflow->getTransition($transition);
        $view       = $this->viewFactory->create(
            $item,
            $transition,
            ['form' => $form, 'errors' => $handler->getContext()->getErrorCollection(), 'module' => $module]
        );

        return $view->render();
    }

    /**
     * Create the transition handler.
     *
     * @param EntityId $entityId   The entity id.
     * @param string   $transition The transition.
     * @param Request  $request    Current request.
     * @param Item     $item       Workflow item.
     * @param Workflow $workflow   Workflow.
     *
     * @throws RuntimeException When no handler could be found.
     */
    protected function createTransitionHandler(
        EntityId $entityId,
        string $transition,
        Request $request,
        Item $item,
        Workflow $workflow
    ): TransitionHandler {
        try {
            if ($item->getWorkflowName() !== $workflow->getName()) {
                $handler = $this->workflowManager->handle($item, $transition, $request->query->getBoolean('detach'));
            } else {
                $handler = $this->workflowManager->handle($item, $transition);
            }
        } catch (WorkflowException $e) {
            throw new RuntimeException(
                sprintf(
                    'Could not perform transition "%s" on entity "%s". Creating handler failed with message "%s".',
                    $transition,
                    (string) $entityId,
                    $e->getMessage()
                ),
                (int) $e->getCode(),
                $e
            );
        }

        if ($handler === null) {
            throw new RuntimeException(
                sprintf(
                    'Could not perform transition "%s" on entity "%s". No handler created.',
                    $transition,
                    (string) $entityId
                )
            );
        }

        return $handler;
    }

    /**
     * Get redirect url.
     *
     * @param EntityId $entityId The entity id.
     * @param Request  $request  The request.
     */
    protected function getRedirectUrl(EntityId $entityId, Request $request): string
    {
        if ($request->query->get('ref')) {
            $url = '/' . urldecode((string) $request->query->get('ref'));
        } else {
            $url = $this->router->generate(
                'netzmacht.contao_workflow.backend.step',
                ['entityId' => (string) $entityId, 'module' => $request->attributes->get('module')]
            );
        }

        return $url;
    }
}
