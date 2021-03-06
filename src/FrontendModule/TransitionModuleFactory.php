<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\ViewFactory;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use function assert;

/**
 * Class TransitionModuleFactory creates an instance of the transition module.
 */
final class TransitionModuleFactory implements ComponentFactory
{
    /**
     * The template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * The translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * The workflow manager.
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
     * The repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * The form factory.
     *
     * @var FormFactory
     */
    private $formFactory;

    /**
     * The view factory.
     *
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The input adapter.
     *
     * @var Adapter<Input>
     */
    private $inputAdapter;

    /**
     * The config adapter.
     *
     * @var Adapter<Config>
     */
    private $configAdapter;

    /**
     * The request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $requestScopeMatcher;

    /**
     * TransitionModuleFactory constructor.
     *
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
     * @param RequestScopeMatcher $requestScopeMatcher The request scope matcher.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
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
        RequestScopeMatcher $requestScopeMatcher
    ) {

        $this->templateEngine      = $templateEngine;
        $this->translator          = $translator;
        $this->workflowManager     = $workflowManager;
        $this->entityManager       = $entityManager;
        $this->repositoryManager   = $repositoryManager;
        $this->formFactory         = $formFactory;
        $this->viewFactory         = $viewFactory;
        $this->inputAdapter        = $inputAdapter;
        $this->configAdapter       = $configAdapter;
        $this->requestScopeMatcher = $requestScopeMatcher;
        $this->requestStack        = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($model): bool
    {
        return $model instanceof ModuleModel && $model->type === 'workflow_transition';
    }

    /**
     * {@inheritDoc}
     */
    public function create($model, string $column): Component
    {
        assert($model instanceof ModuleModel);

        return new TransitionModule(
            $model,
            $this->templateEngine,
            $this->translator,
            $this->workflowManager,
            $this->entityManager,
            $this->repositoryManager,
            $this->formFactory,
            $this->viewFactory,
            $this->requestStack,
            $this->inputAdapter,
            $this->configAdapter,
            $this->requestScopeMatcher,
            $column
        );
    }
}
