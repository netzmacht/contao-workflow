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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Workflow\View\View;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class StateHistoryRenderer
 *
 * @package Netzmacht\ContaoWorkflowBundle\Workflow\View\Renderer
 */
class StateHistoryRenderer extends AbstractRenderer
{
    /**
     * The section name.
     *
     * @var string
     */
    protected static $section = 'history';

    /**
     * The workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Config adapter.
     *
     * @var Adapter|Config
     */
    private $configAdapter;

    public function __construct(Manager $manager, Translator $translator, $configAdapter, array $templates = [])
    {
        parent::__construct($translator, $templates);

        $this->manager       = $manager;
        $this->configAdapter = $configAdapter;
    }


    /**
     * {@inheritdoc}
     */
    public function supports(Workflow $workflow, $context): bool
    {
        return $context instanceof Step;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderParameters(View $view): array
    {
        $workflow = $view->getWorkflow();
        $history = $view->getItem()->getStateHistory();
        $data    = [];
        $stateColumns = StringUtil::deserialize($workflow->getConfigValue('stepHistoryColumns'), true);

        foreach ($history as $state) {
            foreach ($stateColumns as $column) {
                $data[$column] = $this->renderStateColumn($state, $column);
            }
        }

        return ['columns' => $stateColumns, 'history' => $data];
    }

    /**
     * @param State  $state
     * @param string $column
     *
     * @return null|string
     */
    private function renderStateColumn(State $state, string $column): ?string
    {
        switch ($column) {
            case 'workflow':
                return $this->renderWorkflowName($state);

            case 'transition':
                return $this->renderTransitionName($state);

            case 'step':
                return $this->renderStepName($state);

            case 'successful':
                $yesNo = $state->isSuccessful() ? 'yes' : 'no';
                return $this->trans('MSC.' . $yesNo, [], 'contao_default');

            case 'reachedAt':
                return $state->getReachedAt()->format($this->configAdapter->get('datimFormat'));
        }

        return null;
    }

    /**
     * Render the workflow name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderWorkflowName(State $state): string
    {
        try {
            return $this->manager->getWorkflowByName($state->getWorkflowName())->getLabel();
        } catch (WorkflowException $e) {
            return $state->getWorkflowName() ?: '-';
        }
    }

    /**
     * Render the transition name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderTransitionName(State $state): string
    {
        try {
            return $this->manager
                ->getWorkflowByName($state->getWorkflowName())
                ->getTransition($state->getTransitionName())
                ->getLabel();
        } catch (WorkflowException $e) {
            return $state->getTransitionName() ?: '-';
        }
    }

    /**
     * Render the step name.
     *
     * @param State $state Workflow item state.
     *
     * @return string
     */
    private function renderStepName(State $state): string
    {
        try {
            return $this->manager
                ->getWorkflowByName($state->getWorkflowName())
                ->getStep($state->getStepName())
                ->getLabel();
        } catch (WorkflowException $e) {
            return $state->getStepName() ?: '-';
        }
    }
}
