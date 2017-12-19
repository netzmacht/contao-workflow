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

use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Permission\PermissionModel;
use Netzmacht\ContaoWorkflowBundle\Model\State\StateModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

array_insert(
    $GLOBALS['BE_MOD']['system'],
    1,
    [
        'workflows'        => [
            'icon'       => 'bundles/netzmachtcontaoworkflow/img/workflow.png',
            'stylesheet' => 'bundles/netzmachtcontaoworkflow/css/backend.css',
            'tables'     => [
                'tl_workflow',
                'tl_workflow_step',
                'tl_workflow_transition',
                'tl_workflow_action',
            ],
        ],
    ]
);


/*
 * Permissions
 */

$GLOBALS['TL_PERMISSIONS'][] = 'workflow';


/*
 * Models
 */

$GLOBALS['TL_MODELS']['tl_workflow']            = WorkflowModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_action']     = ActionModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_step']       = StepModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_state']      = StateModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_transition'] = TransitionModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_permission'] = PermissionModel::class;
