<?php

array_insert($GLOBALS['BE_MOD'], 1, array
(
    'workflow' => array(
        'workflow' => array(
            'tables' => array(
                'tl_workflow',
                'tl_workflow_step',
                'tl_workflow_transition',
                'tl_workflow_action'
            ),
        )
    )
));

$GLOBALS['WORKFLOW_TYPES']['empty'] = '';