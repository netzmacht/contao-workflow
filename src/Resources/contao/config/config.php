<?php

declare(strict_types=1);

use Contao\ArrayUtil;
use Netzmacht\ContaoWorkflowBundle\Model\Action\ActionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Permission\PermissionModel;
use Netzmacht\ContaoWorkflowBundle\Model\State\StateModel;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'workflow' => [
            'workflows' => [
                'tables'     => [
                    'tl_workflow',
                    'tl_workflow_step',
                    'tl_workflow_transition',
                    'tl_workflow_action',
                    'tl_workflow_transition_conditional_transition',
                ],
            ],
        ],
    ],
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
