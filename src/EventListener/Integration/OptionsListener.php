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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Integration;

use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use function array_merge;
use function asort;

/**
 * Class OptionsListener handles the options callback for the default workflow integration.
 */
final class OptionsListener
{
    /**
     * Workflow manager.
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * OptionsListener constructor.
     *
     * @param WorkflowManager $workflowManager Workflow manager.
     * @param Connection      $connection      Database connection.
     */
    public function __construct(WorkflowManager $workflowManager, Connection $connection)
    {
        $this->workflowManager = $workflowManager;
        $this->connection      = $connection;
    }

    /**
     * Get the workflow options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array
     */
    public function workflowOptions(DataContainer $dataContainer = null): array
    {
        $names = [];

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            if ($dataContainer && $dataContainer->id && $workflow->getProviderName() !== $dataContainer->table) {
                continue;
            }

            $workflowName         = $workflow->getName();
            $names[$workflowName] = sprintf('%s [%s]', $workflow->getLabel(), $workflowName);
        }

        return $names;
    }

    /**
     * Get the step options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array
     */
    public function stepOptions(DataContainer $dataContainer = null): array
    {
        $options = [];

        if ($dataContainer && $dataContainer->id) {
            try {
                $selectedWorkflow = $this->getSelectedWorkflow($dataContainer);
                if (!$selectedWorkflow) {
                    return [];
                }

                $workflow = $this->workflowManager->getWorkflowByName($selectedWorkflow);
            } catch (WorkflowNotFound $exception) {
                return [];
            }

            return $this->buildWorkflowStepOptions($workflow);
        }

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            // We are in the list view and options are used as filter. Nested options are not supported
            if ($dataContainer) {
                $options[] = $this->buildWorkflowStepOptions($workflow, $workflow->getLabel() . ' | ');
            } else {
                $workflowName = sprintf(
                    '%s [%s]',
                    $workflow->getLabel(),
                    $workflow->getName()
                );

                $options[$workflowName] = $this->buildWorkflowStepOptions($workflow);
            }
        }

        if ($dataContainer && count($options) > 0) {
            $options = array_merge(... $options);
            asort($options);
        }

        return $options;
    }

    /**
     * Get the step permission options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array
     */
    public function stepPermissionOptions(?DataContainer $dataContainer = null): array
    {
        if (!$dataContainer || !$dataContainer->activeRecord || $dataContainer->activeRecord->workflowStep) {
            return [];
        }

        try {
            $selectedWorkflow = $this->getSelectedWorkflow($dataContainer);
            if (!$selectedWorkflow) {
                return [];
            }

            $workflow = $this->workflowManager->getWorkflowByName($selectedWorkflow);
        } catch (WorkflowNotFound $exception) {
            return [];
        }

        $stepName = $dataContainer->activeRecord->workflowStep;
        if (!$workflow->hasStep($stepName)) {
            return [];
        }

        $permission = $workflow->getStep($stepName)->getPermission();
        if ($permission === null) {
            return [];
        }

        return [$permission->__toString() => $this->getPermissionLabel($permission, $workflow)];
    }

    /**
     * Get the selected workflow.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string|null
     */
    private function getSelectedWorkflow(DataContainer $dataContainer): ?string
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('workflow')
            ->from($dataContainer->table)
            ->where('id=:id')
            ->setParameter('id', $dataContainer->id)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn() ?: null;
    }

    /**
     * Build workflow step options from a workflow.
     *
     * @param Workflow $workflow Workflow.
     * @param string   $prefix   Add a prefix before the step options.
     *
     * @return array
     */
    private function buildWorkflowStepOptions(Workflow $workflow, string $prefix = ''): array
    {
        $options = [];

        foreach ($workflow->getTransitions() as $transition) {
            $stepTo = $transition->getStepTo();
            if (!$stepTo) {
                continue;
            }

            $options[$stepTo->getName()] = $prefix . $stepTo->getLabel();
        }

        return $options;
    }

    /**
     * Get the label for the permission.
     *
     * @param Permission $permission The permission.
     * @param Workflow   $workflow   The related workflow.
     *
     * @return string
     */
    private function getPermissionLabel(Permission $permission, Workflow $workflow): string
    {
        $label       = $permission->getPermissionId();
        $permissions = StringUtil::deserialize($workflow->getConfigValue('permissions'), true);
        foreach ($permissions as $config) {
            if ($config['name'] === $permission->getPermissionId() && $config['label']) {
                $label = $config['label'];
            }
        }

        return $label;
    }
}
