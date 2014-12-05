<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;

use Netzmacht\Workflow\Contao\Model\RoleModel;
use Netzmacht\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Contao\Model\WorkflowModel;

/**
 * Class Transition used for tl_workflow_transition callbacks.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Transition
{
    /**
     * @var \Database
     */
    private $database;

    /**
     *
     */
    public function __construct()
    {
        $this->database = $GLOBALS['container']['database.connection'];
    }

    /**
     * Get steps which can be a target.
     *
     * @return array
     */
    public function getStepsTo()
    {
        $steps      = array();
        $collection = StepModel::findAll(array('order' => 'name'));

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
     * @param $dc
     *
     * @return array
     */
    public function getEntityProperties($dc)
    {
        if ($dc->activeRecord) {
            $workflow = WorkflowModel::findByPk($dc->activeRecord->pid);

            if ($workflow) {
                return array_map(
                    function($info) {
                        return $info['name'];
                    },
                    array_filter(
                        $this->database->listFields($workflow->providerName),
                        function($info) {
                            return $info['type'] !== 'index';
                        }
                    )
                );
            }
        }

        return array();
    }
}
