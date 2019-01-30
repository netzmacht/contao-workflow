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

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;

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
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

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
     * @param EntityManager   $entityManager   Entity manager.
     * @param Connection      $connection      Database connection.
     */
    public function __construct(WorkflowManager $workflowManager, EntityManager $entityManager, Connection $connection)
    {
        $this->workflowManager = $workflowManager;
        $this->entityManager   = $entityManager;
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
        $names    = [];
        $entityId = null;
        $entity   = null;

        if ($dataContainer && $dataContainer->id) {
            $repository = $this->entityManager->getRepository($dataContainer->table);
            $entityId   = EntityId::fromProviderNameAndId($dataContainer->table, (int) $dataContainer->id);
            $entity     = $repository->find($entityId->getIdentifier());
        }

        foreach ($this->workflowManager->getWorkflows() as $workflow) {
            if ($entityId && !$workflow->supports($entityId, $entity)) {
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
            $workflowName = sprintf(
                '%s [%s]',
                $workflow->getLabel(),
                $workflow->getName()
            );

            $options[$workflowName] = $this->buildWorkflowStepOptions($workflow);
        }

        return $options;
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
            ->select('workflowDefault')
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
     *
     * @return array
     */
    private function buildWorkflowStepOptions(Workflow $workflow): array
    {
        $options = [];

        foreach ($workflow->getTransitions() as $transition) {
            $stepTo = $transition->getStepTo();
            if (!$stepTo) {
                continue;
            }

            $options[$stepTo->getName()] = $stepTo->getLabel();
        }

        return $options;
    }
}
