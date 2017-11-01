<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Netzmacht\Contao\Workflow\Model\StepModel;
use Netzmacht\Contao\Workflow\Model\WorkflowModel;
use Netzmacht\Contao\Workflow\ServiceContainerTrait;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Transition
{
    use ServiceContainerTrait;

    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->database = $this->getServiceContainer()->getDatabaseConnection();
    }

    /**
     * Adjust the input mask.
     *
     * @return void
     */
    public function adjustEditMask()
    {
        $workflow     = WorkflowModel::findByPk(CURRENT_ID);
        $typeProvider = $this->getServiceProvider()->getTypeProvider();

        if (!$workflow || !$typeProvider->hasType($workflow->type)) {
            return;
        }

        $workflowType = $typeProvider->getType($workflow->type);
        if ($workflowType->hasFixedTransitions()) {
            $GLOBALS['TL_DCA']['tl_workflow_transition']['fields']['name']['inputType']                  = 'select';
            $GLOBALS['TL_DCA']['tl_workflow_transition']['fields']['name']['options']                    = $workflowType->getTransitionNames();
            $GLOBALS['TL_DCA']['tl_workflow_transition']['fields']['name']['eval']['includeBlankOption'] = true;
        } else {
            $GLOBALS['TL_DCA']['tl_workflow_transition']['fields']['name']['save_callback'][] = array(
                'Netzmacht\Contao\Workflow\Backend\Common',
                'createName'
            );
        }
    }

    /**
     * Get steps which can be a target.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getStepsTo($dataContainer)
    {
        $steps      = array();
        $collection = StepModel::findBy(['pid=?'], [$dataContainer->activeRecord->pid], ['order' => 'name']);

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
    public function getEntityProperties($dataContainer)
    {
        if ($dataContainer->activeRecord) {
            $workflow = WorkflowModel::findByPk($dataContainer->activeRecord->pid);

            if ($workflow) {
                return array_map(
                    function ($info) {
                        return $info['name'];
                    },
                    array_filter(
                        $this->database->listFields($workflow->providerName),
                        function ($info) {
                            return $info['type'] !== 'index';
                        }
                    )
                );
            }
        }

        return array();
    }
}
