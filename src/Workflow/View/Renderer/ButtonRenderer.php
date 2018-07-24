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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Step;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class ButtonRenderer
 */
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

    /**
     * {@inheritDoc}
     */
    public function supports(View $view): bool
    {
        return $view->getContext() instanceof Step;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderParameters(View $view): array
    {
        /** @var Step $step */
        $step     = $view->getContext();
        $workflow = $view->getWorkflow();
        $actions  = [];
        $params   = [];
        $request  = $this->requestStack->getCurrentRequest();
        $context  = new Context();

        if ($this->scopeMatcher->isBackendRequest($request)) {
            if ($request->query->get('module')) {
                $params['module'] = $request->query->get('module');
            }
        }

        if ($view->getItem()->getWorkflowName() !== $view->getWorkflow()->getName()) {
            $params['detach'] = true;
        }

        foreach ($step->getAllowedTransitions() as $transitionName) {
            if (!$workflow->hasTransition($transitionName)) {
                continue;
            }

            $transition = $workflow->getTransition($transitionName);

            if ($transition->isAllowed($view->getItem(), $context) && !$transition->getConfigValue('hide')) {
                $actions[] = $this->buildAction($view, $transitionName, $params);
            }
        }

        return [
            'actions' => $actions
        ];
    }

    /**
     * Build the transition action.
     *
     * @param View   $view           The current view.
     * @param string $transitionName The transition name.
     * @param array  $params         Default route params.
     *
     * @return array
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
                    'transition' => $transitionName
                ]
            ),
            'icon'   => $transition->getConfigValue('icon') ?: null,
        ];
    }
}
