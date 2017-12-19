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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
class TransitionCallbackListener extends AbstractListener
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $name = 'tl_workflow_transition';

    /**
     * Type provider.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Transition constructor.
     *
     * @param WorkflowTypeRegistry $typeRegistry      Workflow type registry.
     * @param DcaManager           $dcaManager        Data container manager.
     * @param RepositoryManager    $repositoryManager Repository manager.
     */
    public function __construct(
        WorkflowTypeRegistry $typeRegistry,
        DcaManager $dcaManager,
        RepositoryManager $repositoryManager
    ) {
        parent::__construct($dcaManager);

        $this->typeRegistry      = $typeRegistry;
        $this->repositoryManager = $repositoryManager;
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
        $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->pid], ['order' => '.label']);

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
                $fields        = array_keys($schemaManager->listTableColumns($workflow->providerName));
                $options       = [];
                $formatter     = $this->getFormatter((string) $workflow->providerName);

                foreach ($fields as $field) {
                    $options[$field] = sprintf(
                        '%s [%s]',
                        $formatter->formatFieldLabel($field),
                        $field
                    );
                }

                return $options;
            }
        }

        return [];
    }
}
