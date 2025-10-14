<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\Input;
use Contao\Model;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Controller\FrontendModule\AbstractFrontendModuleController;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;
use Netzmacht\ContaoWorkflowBundle\Form\TransitionFormType;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedEntity;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Override;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function in_array;
use function sprintf;

#[AsFrontendModule('workflow_transition', category: 'workflow', template: 'mod_workflow_transition')]
final class TransitionFrontendModuleController extends AbstractFrontendModuleController
{
    /**
     * @param Adapter<Input>  $inputAdapter
     * @param Adapter<Config> $configAdapter
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        TranslatorInterface $translator,
        private readonly WorkflowManager $workflowManager,
        private readonly EntityManager $entityManager,
        private readonly RepositoryManager $repositoryManager,
        private readonly FormFactory $formFactory,
        private readonly ViewFactory $viewFactory,
        private readonly Adapter $inputAdapter,
        private readonly Adapter $configAdapter,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $router, $translator);
    }

    /** {@inheritDoc} */
    #[Override]
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
        $entityId = $this->getEntityId($model);
        $item     = $this->createItem($entityId);
        $workflow = $this->getWorkflowByItem($item);

        /** @psalm-suppress PossiblyInvalidCast */
        $transition = (string) $this->inputAdapter->get('transition');
        $handler    = $this->createTransitionHandler($model, $entityId, $transition, $item, $workflow);
        $payload    = [];
        $validForm  = true;
        $form       = null;

        if ($handler->getRequiredPayloadProperties()) {
            $form = $this->formFactory->create(
                TransitionFormType::class,
                [],
                ['handler' => $handler, 'item' => $item],
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
                return $this->createRedirectResponse($model, $request);
            }
        }

        $transition = $workflow->getTransition($transition);
        $view       = $this->viewFactory->create(
            $item,
            $transition,
            ['form' => $form, 'errors' => $handler->getContext()->getErrorCollection()],
        );

        $request->attributes->set(View::class, $view);

        return null;
    }

    /** {@inheritDoc} */
    #[Override]
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data         = parent::prepareTemplateData($data, $request, $model);
        $data['view'] = $request->attributes->get(View::class);

        return $data;
    }

    /**
     * Get the entity id from the request.
     *
     * @throws RuntimeException When an invalid entity is given or the data provider is not supported.
     */
    private function getEntityId(ModuleModel $model): EntityId
    {
        try {
            $entityId = $this->inputAdapter->get('auto_item');
            /** @psalm-suppress PossiblyInvalidCast */
            $entityId = EntityId::fromString((string) $entityId);
        } catch (AssertionFailed $exception) {
            throw new RuntimeException('Invalid entity id given.', $exception->getCode(), $exception);
        }

        $supportedProviders = StringUtil::deserialize($model->workflow_providers, true);
        if (! in_array($entityId->getProviderName(), $supportedProviders)) {
            throw new RuntimeException(sprintf('Unsupported data provider "%s"', $entityId->getProviderName()));
        }

        return $entityId;
    }

    /**
     * Find the entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @throws PageNotFoundException If an entity is not found.
     */
    protected function createItem(EntityId $entityId): Item
    {
        try {
            $repository = $this->entityManager->getRepository($entityId->getProviderName());
            $entity     = $repository->find($entityId->getIdentifier());
        } catch (UnsupportedEntity $e) {
            throw new PageNotFoundException(
                sprintf('Entity "%s" not found.', (string) $entityId),
                $e->getCode(),
                $e,
            );
        }

        return $this->workflowManager->createItem($entityId, $entity);
    }

    /**
     * Get workflow by the entity.
     *
     * @param Item $item The entity.
     *
     * @throws BadRequestHttpException When no workflow was found.
     */
    private function getWorkflowByItem(Item $item): Workflow
    {
        try {
            return $this->workflowManager->getWorkflowByItem($item);
        } catch (WorkflowNotFound $exception) {
            throw new BadRequestHttpException(
                sprintf('No workflow found for entity "%s"', (string) $item->getEntityId()),
                $exception,
            );
        }
    }

    /**
     * Create the transition handler.
     *
     * @param EntityId $entityId   The entity id.
     * @param string   $transition The transition.
     * @param Item     $item       Workflow item.
     * @param Workflow $workflow   Workflow.
     *
     * @throws RuntimeException When no handler could be found.
     */
    protected function createTransitionHandler(
        ModuleModel $model,
        EntityId $entityId,
        string $transition,
        Item $item,
        Workflow $workflow,
    ): TransitionHandler {
        try {
            if ($item->getWorkflowName() !== $workflow->getName()) {
                $handler = $this->workflowManager->handle($item, $transition, (bool) $model->workflow_detach);
            } else {
                $handler = $this->workflowManager->handle($item, $transition);
            }
        } catch (WorkflowException $e) {
            throw new RuntimeException(
                sprintf(
                    'Could not perform transition "%s" on entity "%s". Creating handler failed with message "%s".',
                    $transition,
                    (string) $entityId,
                    $e->getMessage(),
                ),
                $e->getCode(),
                $e,
            );
        }

        if ($handler === null) {
            throw new RuntimeException(
                sprintf(
                    'Could not perform transition "%s" on entity "%s". No handler created.',
                    $transition,
                    (string) $entityId,
                ),
            );
        }

        return $handler;
    }

    /**
     * Redirect to new location.
     */
    private function createRedirectResponse(ModuleModel $model, Request $request): Response
    {
        if ($model->jumpTo) {
            $page = $this->repositoryManager->getRepository(PageModel::class)->find((int) $model->jumpTo);
            if ($page instanceof PageModel) {
                return new RedirectResponse(
                    $this->contentUrlGenerator->generate($page, referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                    Response::HTTP_SEE_OTHER,
                );
            }
        }

        throw new RedirectResponseException($request->getRequestUri());
    }
}
