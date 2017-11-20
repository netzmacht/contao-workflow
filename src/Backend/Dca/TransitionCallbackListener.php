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

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Contao\Workflow\Model\StepModel;
use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeProvider;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class TransitionCallbackListener
{
    /**
     * Type provider.
     *
     * @var WorkflowTypeProvider
     */
    private $typeProvider;

    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Transition constructor.
     *
     * @param WorkflowTypeProvider $typeProvider
     * @param DcaManager           $dcaManager
     * @param RepositoryManager    $repositoryManager
     */
    public function __construct(
        WorkflowTypeProvider $typeProvider,
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager
    ) {
        $this->typeProvider      = $typeProvider;
        $this->dcaManager        = $dcaManager;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Adjust the input mask.
     *
     * @return void
     */
    public function adjustEditMask(): void
    {
        $workflow = $this->repositoryManager->getRepository(WorkflowModel::class)->find((int) CURRENT_ID);

        if (!$workflow || !$this->typeProvider->hasType($workflow->type)) {
            return;
        }

        $workflowType = $this->typeProvider->getType($workflow->type);
        $definition   = $this->dcaManager->getDefinition('tl_workflow_transition');

        if ($workflowType->hasFixedTransitions()) {
            $dca = (array) $definition->get(['fields', 'name']);

            $dca['inputType']                  = 'select';
            $dca['options']                    = $workflowType->getTransitionNames();
            $dca['eval']['includeBlankOption'] = true;

            $definition->set(['fields', 'name'], $dca);
        } else {
            $callbacks   = $definition->get(['fields', 'name', 'save_callback']);
            $callbacks[] = ['netzmacht.contao_workflow.listeners.dca.common', 'createName'];

            $definition->set(['fields', 'name', 'save_callback'], $callbacks);
        }
    }

    /**
     * Get steps which can be a target.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getStepsTo($dataContainer): array
    {
        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);
        $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['order' => 'name']);

        if ($collection) {
            while ($collection->next()) {
                $steps[$collection->id] = $collection->label;

                if ($collection->final) {
                    $steps[$collection->id] .= ' [final]';
                }
            }
        }

        return $steps;
    }

    /**
     * Get entity properties.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getEntityProperties($dataContainer): array
    {
        if ($dataContainer->activeRecord) {
            $repository = $this->repositoryManager->getRepository(WorkflowModel::class);
            $workflow   = $repository->find((int) $dataContainer->activeRecord->pid);

            if ($workflow) {
                $schemaManager = $this->repositoryManager->getConnection()->getSchemaManager();

                return array_map(
                    function ($info) {
                        return $info['name'];
                    },
                    array_keys($schemaManager->listTableColumns($workflow->providerName))
                );
            }
        }

        return [];
    }
}
