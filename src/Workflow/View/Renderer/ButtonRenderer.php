<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_merge;
use function assert;

final class ButtonRenderer extends AbstractRenderer
{
    /**
     * The section name.
     *
     * @var string
     */
    protected static $section = 'buttons';

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Current web request.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        Translator $translator,
        RequestScopeMatcher $scopeMatcher,
        RequestStack $requestStack,
        array $templates = []
    ) {
        parent::__construct($translator, $templates);

        $this->scopeMatcher = $scopeMatcher;
        $this->requestStack = $requestStack;
    }

    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Step;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        $step = $view->getContext();
        assert($step instanceof Step);
        $workflow = $view->getWorkflow();
        $params   = [];
        $request  = $this->requestStack->getCurrentRequest();
        $context  = new Context();

        if ($request && $this->scopeMatcher->isBackendRequest($request)) {
            $params['module'] = $request->attributes->get('module');
        }

        if ($view->getItem()->getWorkflowName() !== $view->getWorkflow()->getName()) {
            $params['detach'] = true;
        }

        return [
            'actions' => $this->buildActions($step, $workflow, $view, $context, $params),
        ];
    }

    /**
     * Build the transition action.
     *
     * @param View                $view           The current view.
     * @param string              $transitionName The transition name.
     * @param array<string,mixed> $params         Default route params.
     *
     * @return array<string,mixed>
     */
    protected function buildAction(View $view, string $transitionName, array $params): array
    {
        $transition = $view->getWorkflow()->getTransition($transitionName);

        return [
            'label'  => $transition->getLabel(),
            'title'  => $transition->getConfigValue('description') ?: $transition->getLabel(),
            'route'  => 'netzmacht.contao_workflow.backend.transition',
            'params' => array_merge(
                $params,
                [
                    'entityId'   => $view->getItem()->getEntityId(),
                    'transition' => $transitionName,
                    'module'     => (string) $view->getOption('module'),
                ]
            ),
            'icon'   => $transition->getConfigValue('icon'),
        ];
    }

    /**
     * Build actions.
     *
     * @param Step|null           $step     The current step.
     * @param Workflow            $workflow The workflow.
     * @param View                $view     The view.
     * @param Context             $context  The context.
     * @param array<string,mixed> $params   The params.
     *
     * @return list<array<string,mixed>>
     */
    protected function buildActions(?Step $step, Workflow $workflow, View $view, Context $context, array $params): array
    {
        $actions = [];
        if (! $step) {
            $transition = $workflow->getStartTransition();
            if ($transition->isAllowed($view->getItem(), $context)) {
                $actions[] = $this->buildAction($view, $transition->getName(), $params);
            }

            return $actions;
        }

        foreach ($step->getAllowedTransitions() as $transitionName) {
            if (! $workflow->hasTransition($transitionName)) {
                continue;
            }

            $transition = $workflow->getTransition($transitionName);

            if (! $transition->isAllowed($view->getItem(), $context) || $transition->getConfigValue('hide')) {
                continue;
            }

            $actions[] = $this->buildAction($view, $transitionName, $params);
        }

        return $actions;
    }
}
