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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\DefaultType;

use Netzmacht\Workflow\Manager\Manager as WorkflowManager;

final class OptionsListener
{
    /**
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * OptionsListener constructor.
     *
     * @param WorkflowManager $workflowManager
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    public function workflowOptions(): array
    {
        $names = [];

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            $workflowName         = $workflow->getName();
            $names[$workflowName] = sprintf('%s [%s]', $workflow->getLabel(), $workflowName);
        }

        return $names;
    }

    public function stepOptions(): array
    {
        $options = [];

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            foreach ($workflow->getTransitions() as $transition) {
                $stepTo = $transition->getStepTo();
                if (!$stepTo) {
                    continue;
                }

                $workflowName = sprintf(
                    '%s [%s]',
                    $workflow->getLabel(),
                    $workflow->getName()
                );

                $options[$workflowName][$stepTo->getName()] = $stepTo->getLabel();
            }
        }

        return $options;
    }
}
