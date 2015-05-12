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


array_insert($GLOBALS['BE_MOD'], 1, array
(
    'workflow' => array(
        'workflows' => array(
            'icon'   => 'system/modules/workflow/assets/img/workflow.png',
            'stylesheet' => 'system/modules/workflow/assets/css/backend.css',
            'tables' => array(
                'tl_workflow',
                'tl_workflow_step',
                'tl_workflow_transition',
                'tl_workflow_action',
            ),
        ),
        'workflow_history' => array(
            'icon'   => 'system/themes/default/images/news.gif',
            'stylesheet' => 'system/modules/workflow/assets/css/backend.css',
            'tables' => array('tl_workflow_state'),
        )
    )
));


/*
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'workflow';


/*
 * Workflow types
 */
$GLOBALS['WORKFLOW_TYPES'][] = 'default';


/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeDependencyContainer'][] = array('Netzmacht\Workflow\Contao\Boot', 'startup');
$GLOBALS['TL_HOOKS']['initializeDependencyContainer'][] = function($container) {
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = $container['event-dispatcher'];
    $eventDispatcher->addSubscriber($GLOBALS['container']['workflow.factory.repository']);
    $eventDispatcher->addSubscriber($GLOBALS['container']['workflow.factory.entity']);
};


/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_workflow']            = 'Netzmacht\Workflow\Contao\Model\WorkflowModel';
$GLOBALS['TL_MODELS']['tl_workflow_action']     = 'Netzmacht\Workflow\Contao\Model\ActionModel';
$GLOBALS['TL_MODELS']['tl_workflow_role']       = 'Netzmacht\Workflow\Contao\Model\RoleModel';
$GLOBALS['TL_MODELS']['tl_workflow_step']       = 'Netzmacht\Workflow\Contao\Model\StepModel';
$GLOBALS['TL_MODELS']['tl_workflow_state']      = 'Netzmacht\Workflow\Contao\Model\StateModel';
$GLOBALS['TL_MODELS']['tl_workflow_transition'] = 'Netzmacht\Workflow\Contao\Model\TransitionModel';
$GLOBALS['TL_MODELS']['tl_workflow_permission'] = 'Netzmacht\Workflow\Contao\Model\PermissionModel';
