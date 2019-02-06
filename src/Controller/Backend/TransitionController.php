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

/**
 * Class TransitController
 */
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

    /**
     * Execute the transition.
     *
     * @param EntityId $entityId   EntityId of current entity.
     * @param string   $transition The transition name.
     * @param Request  $request    The web request.
     *
     * @return Response
     *
     * @throws \RuntimeException For any runtime exception.
     * @throws WorkflowException When the workflow handling fails.
     */
    public function __invoke(EntityId $entityId, string $transition, Request $request): Response
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
                null,
                ['transition' => $handler->getTransition()]
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
            ['form' => $form, 'errors' => $handler->getContext()->getErrorCollection()]
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
     * @return \Netzmacht\Workflow\Handler\TransitionHandler
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
                    $entityId,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        if ($handler === null) {
            throw new RuntimeException(
                sprintf('Could not perform transition "%s" on entity "%s". No handler created.', $transition, $entityId)
            );
        }

        return $handler;
    }

    /**
     * Get redirect url.
     *
     * @param EntityId $entityId The entity id.
     * @param Request  $request  The request.
     *
     * @return string
     */
    protected function getRedirectUrl(EntityId $entityId, Request $request): string
    {
        if ($request->query->get('ref')) {
            $url = '/' . urldecode($request->query->get('ref'));
        } else {
            $url = $this->router->generate(
                'netzmacht.contao_workflow.backend.step',
                ['entityId' => (string) $entityId, 'module' => $request->get('module')]
            );
        }

        return $url;
    }
}
