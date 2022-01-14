<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Contao\Model;
use Contao\PageModel;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
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
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

use function assert;
use function in_array;
use function sprintf;

/**
 * Class TransitionModule processes a transition for an entity.
 *
 * @psalm-suppress DeprecatedInterface
 */
final class TransitionModule extends AbstractModule
{
    /**
     * Name of the template.
     *
     * @var string
     */
    protected $templateName = 'mod_workflow_transition';

    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * View factory.
     *
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * The form factory.
     *
     * @var FormFactory
     */
    private $formFactory;

    /**
     * The config adapter.
     *
     * @var Adapter<Config>
     */
    private $configAdapter;

    /**
     * The input adapter.
     *
     * @var Adapter<Input>
     */
    private $inputAdapter;

    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * The generated view.
     *
     * @var View|null
     */
    private $view;

    // phpcs:disable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup
    /**
     * @psalm-suppress DeprecatedClass
     *
     * @param Model               $model               The module model.
     * @param TemplateEngine      $templateEngine      The template engine.
     * @param Translator          $translator          The translator.
     * @param WorkflowManager     $workflowManager     The workflow manager.
     * @param EntityManager       $entityManager       The entity manager.
     * @param RepositoryManager   $repositoryManager   The repository manager.
     * @param FormFactory         $formFactory         The form factory.
     * @param ViewFactory         $viewFactory         The view factory.
     * @param RequestStack        $requestStack        The request stack.
     * @param Adapter             $inputAdapter        The input adapter.
     * @param Adapter             $configAdapter       The config adapter.
     * @param string              $column              The section or column name.
     * @param RequestScopeMatcher $requestScopeMatcher The request scope matcher.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    // phpcs:enable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup
    public function __construct(
        Model $model,
        TemplateEngine $templateEngine,
        Translator $translator,
        WorkflowManager $workflowManager,
        EntityManager $entityManager,
        RepositoryManager $repositoryManager,
        FormFactory $formFactory,
        ViewFactory $viewFactory,
        RequestStack $requestStack,
        Adapter $inputAdapter,
        Adapter $configAdapter,
        RequestScopeMatcher $requestScopeMatcher,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $translator, $column, $requestScopeMatcher);

        $this->workflowManager   = $workflowManager;
        $this->entityManager     = $entityManager;
        $this->repositoryManager = $repositoryManager;
        $this->formFactory       = $formFactory;
        $this->viewFactory       = $viewFactory;
        $this->requestStack      = $requestStack;
        $this->inputAdapter      = $inputAdapter;
        $this->configAdapter     = $configAdapter;
    }

    protected function compile(): void
    {
        $entityId   = $this->getEntityId();
        $item       = $this->createItem($entityId);
        $workflow   = $this->getWorkflowByItem($item);
        $transition = (string) $this->inputAdapter->get('transition');
        $handler    = $this->createTransitionHandler($entityId, $transition, $item, $workflow);
        $payload    = [];
        $validForm  = true;
        $form       = null;

        if ($handler->getRequiredPayloadProperties()) {
            $request = $this->requestStack->getCurrentRequest();
            $form    = $this->formFactory->create(
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
                $this->redirect();
            }
        }

        $transition = $workflow->getTransition($transition);
        $this->view = $this->viewFactory->create(
            $item,
            $transition,
            ['form' => $form, 'errors' => $handler->getContext()->getErrorCollection()]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        assert($this->view instanceof View);

        $data         = parent::prepareTemplateData($data);
        $data['view'] = $this->view->render()->getContent();

        return $data;
    }

    /**
     * Get the entity id from the request.
     *
     * @throws RuntimeException When an invalid entity is given or the data provider is not supported.
     */
    private function getEntityId(): EntityId
    {
        try {
            $entityId = $this->configAdapter->get('useAutoItem')
                ? $this->inputAdapter->get('auto_item')
                : $this->inputAdapter->get('entityId');

            $entityId = EntityId::fromString($entityId);
        } catch (AssertionFailed $exception) {
            throw new RuntimeException('Invalid entity id given.', (int) $exception->getCode(), $exception);
        }

        $supportedProviders = StringUtil::deserialize($this->get('workflow_providers'), true);
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
     * @throws PageNotFoundException If entity is not found.
     */
    protected function createItem(EntityId $entityId): Item
    {
        try {
            $repository = $this->entityManager->getRepository($entityId->getProviderName());
            $entity     = $repository->find($entityId->getIdentifier());
        } catch (UnsupportedEntity $e) {
            throw new PageNotFoundException(
                sprintf('Entity "%s" not found.', (string) $entityId),
                (int) $e->getCode(),
                $e
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
                $exception
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
        EntityId $entityId,
        string $transition,
        Item $item,
        Workflow $workflow
    ): TransitionHandler {
        try {
            if ($item->getWorkflowName() !== $workflow->getName()) {
                $handler = $this->workflowManager->handle($item, $transition, (bool) $this->get('workflow_detach'));
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
     * Redirect to new location.
     *
     * @throws RedirectResponseException To interrupt contao page rendering and do the redirect.
     */
    private function redirect(): void
    {
        if ($this->get('jumpTo')) {
            $page = $this->repositoryManager->getRepository(PageModel::class)->find((int) $this->get('jumpTo'));
            if ($page instanceof PageModel) {
                throw new RedirectResponseException($page->getAbsoluteUrl());
            }
        }

        $request = $this->requestStack->getCurrentRequest();
        assert($request instanceof Request);

        throw new RedirectResponseException($request->getRequestUri());
    }
}
