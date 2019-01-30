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

use Netzmacht\ContaoWorkflowBundle\Form\TransitionFormType;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowException;
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
        $item     = $this->createItem($entityId);
        $workflow = $this->workflowManager->getWorkflowByItem($item);

        if ($item->getWorkflowName() !== $workflow->getName()) {
            $handler = $this->workflowManager->handle($item, $transition, $request->query->getBoolean('detach'));
        } else {
            $handler = $this->workflowManager->handle($item, $transition);
        }

        $payload   = [];
        $validForm = true;

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
            $handler->transit();

            if ($request->query->get('ref')) {
                $url = '/' . urldecode($request->query->get('ref'));
            } else {
                $url = $this->router->generate(
                    'netzmacht.contao_workflow.backend.step',
                    ['entityId' => (string) $entityId, 'module' => $request->get('module')]
                );
            }

            return new RedirectResponse($url);
        }

        if (!isset($form)) {
            // TODO: Use proper exception.
            throw new \RuntimeException();
        }

        $transition = $workflow->getTransition($transition);
        $view       = $this->viewFactory->create($item, $transition, ['form' => $form]);

        return $view->render();
    }
}
