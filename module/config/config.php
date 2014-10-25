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
                'tl_workflow_action'
            ),
        )
    )
));


/*
 * Workflow types
 */
$GLOBALS['WORKFLOW_TYPES']['default'] = '';


/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_workflow_step'] = 'Netzmacht\Contao\Workflow\Contao\Model\StepModel';