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

use Netzmacht\Contao\Workflow\Form\TransitionFormType;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface as Router;

/**
 * Class TransitController
 */
class TransitionController extends AbstractController
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
        WorkflowTypeRegistry $typeRegistry,
        TemplateEngine $renderer,
        Router $router,
        FormFactory $formFactory
    ) {
        parent::__construct($workflowManager, $entityManager, $typeRegistry, $renderer, $router);

        $this->formFactory = $formFactory;
    }

    /**
     * Execute the transition.
     *
     * @param EntityId $entityId
     * @param string   $transition
     * @param Request  $request
     *
     * @return Response
     */
    public function execute(EntityId $entityId, string $transition, Request $request): Response
    {
        $item     = $this->createItem($entityId);
        $workflow = $this->workflowManager->getWorkflowByItem($item);

        if ($item->getWorkflowName() !== $workflow->getName()) {
            $handler = $this->workflowManager->handle($item, $transition, (bool) $request->query->get('detach'));
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

            return new RedirectResponse(
                $this->router->generate('netzmacht.contao_workflow.backend.step', ['entityId' => (string) $entityId])
            );
        }

        if (!isset($form)) {
            throw new \RuntimeException();
        }

        $renderer = $this->typeRegistry->getType($workflow->getConfigValue('type'))->getRenderer();

        return $this->renderer->renderResponse(
            '@NetzmachtContaoWorkflow/backend/transition.html.twig',
            [
                'headline'    => $this->generateHeadline($handler),
                'subheadline' => $renderer->renderLabel($item),
                'errors'      => [],
                'form'        => $form->createView(),
                'transition'  => $handler->getTransition(),
            ]
        );
    }

    /**
     * Generate the headline.
     *
     * @param TransitionHandler $handler The transition handler.
     *
     * @return string
     */
    private function generateHeadline(TransitionHandler $handler): string
    {
        $headline = $handler->getWorkflow()->getLabel();

        if ($handler->getCurrentStep()) {
            $headline .= ' &raquo; ' . $handler->getCurrentStep()->getLabel();
        }

        $headline .= ' &raquo; ' . $handler->getTransition()->getLabel();

        return $headline;
    }
}
