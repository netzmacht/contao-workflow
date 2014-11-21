<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Model;

/**
 * Class StateModel.
 *
 * @package Netzmacht\Workflow\Contao\Model
 *
 * @property string       $workflowName   The name of the workflow.
 * @property string       $entityId       The entity id string representation.
 * @property string       $transitionName The name of the last transition.
 * @property string       $stepName       The name of the current step.
 * @property bool         $success        The success state.
 * @property string|array $errors         Json encoded list of errors.
 * @property string|array $data           Json encoded workflow data.
 * @property int          $reachedAt      Timestamp when step was reached.
 */
class StateModel extends \Model
{
    /**
     * The workflow state table.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_state';
}
