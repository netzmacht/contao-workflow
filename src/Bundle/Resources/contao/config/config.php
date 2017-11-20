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
            'icon'   => 'bundles/netzmachtcontaoworkflow/img/workflow.png',
            'stylesheet' => 'bundles/netzmachtcontaoworkflow/css/backend.css',
            'tables' => array(
                'tl_workflow',
                'tl_workflow_step',
                'tl_workflow_transition',
                'tl_workflow_action',
            ),
        ),
        'workflow_history' => array(
            'icon'   => 'system/themes/default/images/news.gif',
            'stylesheet' => 'bundles/netzmachtcontaoworkflow/css/backend.css',
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
$GLOBALS['WORKFLOW_TYPES'][] = 'Netzmacht\Contao\Workflow\Type\DefaultWorkflowType';


/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeDependencyContainer'][] = array('Netzmacht\Contao\Workflow\Boot', 'startup');
$GLOBALS['TL_HOOKS']['initializeDependencyContainer'][] = function($container) {
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = $container['event-dispatcher'];
    $eventDispatcher->addSubscriber($GLOBALS['container']['workflow.factory.repository']);
    $eventDispatcher->addSubscriber($GLOBALS['container']['workflow.factory.entity']);
};


/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_workflow']            = \Netzmacht\Contao\Workflow\Model\Workflow\WorkflowModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_action']     = \Netzmacht\Contao\Workflow\Model\Action\ActionModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_step']       = \Netzmacht\Contao\Workflow\Model\Step\StepModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_state']      = \Netzmacht\Contao\Workflow\Model\State\StateModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_transition'] = \Netzmacht\Contao\Workflow\Model\Transition\TransitionModel::class;
$GLOBALS['TL_MODELS']['tl_workflow_permission'] = \Netzmacht\Contao\Workflow\Model\Permission\PermissionModel::class;
