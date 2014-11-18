<?php

/*
 * Backend module
 */
array_insert($GLOBALS['BE_MOD'], 1, array
(
    'workflow' => array(
        'workflow' => array(
            'icon'   => 'system/modules/workflow/assets/img/workflow.png',
            'tables' => array(
                'tl_workflow',
                'tl_workflow_step',
                'tl_workflow_transition',
                'tl_workflow_action',
            ),
        ),
        'workflow_history' => array(
            'icon'   => 'system/themes/default/images/news.gif',
            'tables' => array('tl_workflow_state'),
        )
    )
));

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'workflow';

/*
 * Workflow types
 */
$GLOBALS['WORKFLOW_TYPES']['default'] = '';

/*
 * Event Subscribers
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Workflow\Contao\Boot';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Workflow\Contao\Flow\ManagerBuilder';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Workflow\Contao\Flow\WorkflowBuilder';

//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Workflow\Contao\BackendSubscriber';
//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\Workflow\Factory\WorkflowBuilder';
//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\Workflow\Factory\EntityFactory';
//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\Workflow\Factory\FormFactory';
//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Netzmacht\Contao\Workflow\Entity\EntityManager';
//
//$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = function() {
//    return new Netzmacht\Contao\Workflow\Transaction\DatabaseTransactionHandler(\Database::getInstance());
//};

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_workflow']            = 'Netzmacht\Workflow\Contao\Model\WorkflowModel';
$GLOBALS['TL_MODELS']['tl_workflow_action']     = 'Netzmacht\Workflow\Contao\Model\ActionModel';
$GLOBALS['TL_MODELS']['tl_workflow_role']       = 'Netzmacht\Workflow\Contao\Model\RoleModel';
$GLOBALS['TL_MODELS']['tl_workflow_step']       = 'Netzmacht\Workflow\Contao\Model\StepModel';
$GLOBALS['TL_MODELS']['tl_workflow_state']      = 'Netzmacht\Workflow\Contao\Model\StateModel';
$GLOBALS['TL_MODELS']['tl_workflow_transition'] = 'Netzmacht\Workflow\Contao\Model\TransitionModel';
