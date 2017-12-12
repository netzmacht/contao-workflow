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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\History;

use Contao\StringUtil;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class StateHistoryRenderer
 */
class StateHistoryRenderer implements HistoryRenderer
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * StateHistoryRenderer constructor.
     *
     * @param Manager    $manager    Workflow manager.
     * @param Translator $translator Translator.
     */
    public function __construct(Manager $manager, Translator $translator)
    {
        $this->manager    = $manager;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Item $item, Workflow $workflow): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function render(Item $item, Workflow $workflow, array $data): array
    {
        $stateColumns = StringUtil::deserialize($workflow->getConfigValue('stepHistoryColumns'), true);

        foreach ($item->getStateHistory() as $index => $state) {
            $data[$index] = [];

            foreach ($stateColumns as $column) {
                switch ($column) {
                    case 'workflow':
                        $value = $this->renderWorkflowName($state);
                        break;

                    case 'transition':
                        $value = $this->renderTransitionName($state);
                        break;

                    case 'step':
                        $value = $this->renderStepName($state);
                        break;

                    case 'successful':
                        $yesNo = $state->isSuccessful() ? 'yes' : 'no';
                        $value = $this->translator->trans('MSC.' . $yesNo, [], 'contao_default');
                        break;
                    default:
                        continue;

                    case 'user':
                }

                $data[$index][$column] = $value;
            }
        }

        return $data;
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
        } catch (WorkflowNotFound $e) {
            return $state->getWorkflowName() ?: '';
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
        } catch (WorkflowNotFound $e) {
            return $state->getTransitionName() ?: '-';
        } catch (TransitionNotFound $e) {
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
        } catch (WorkflowNotFound $e) {
            return '';
        } catch (StepNotFoundException $e) {
            return '';
        }
    }
}
