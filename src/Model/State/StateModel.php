<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\State;

use Contao\Model;

/**
 * @property string|int   $id
 * @property string|int   $tstamp
 * @property string       $workflowName       The name of the start workflow.
 * @property string       $entityId           The entity id string representation.
 * @property string       $transitionName     The name of the last transition.
 * @property string       $stepName           The name of the current step.
 * @property string|bool  $success            The success state.
 * @property string       $errors             Json encoded list of errors.
 * @property string       $data               Json encoded workflow data.
 * @property string|int   $reachedAt          Timestamp when step was reached.
 * @property string|null  $targetWorkflowName The name of the target workflow.
 */
final class StateModel extends Model
{
    /**
     * The workflow state table.
     *
     * @var string
     */
    protected static $strTable = 'tl_workflow_state';
}
