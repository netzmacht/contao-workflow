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

/*
 * Legends
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['name_legend']        = 'Workflow Action';
$GLOBALS['TL_LANG']['tl_workflow_action']['description_legend'] = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_action']['config_legend']      = 'Config';

/*
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['new'][0]    = 'New action';
$GLOBALS['TL_LANG']['tl_workflow_action']['new'][1]    = 'Create new action';
$GLOBALS['TL_LANG']['tl_workflow_action']['edit'][0]   = 'Edit action';
$GLOBALS['TL_LANG']['tl_workflow_action']['edit'][1]   = 'Edit action %s';
$GLOBALS['TL_LANG']['tl_workflow_action']['delete'][0] = 'Delete action';
$GLOBALS['TL_LANG']['tl_workflow_action']['delete'][1] = 'Delete action %s';
$GLOBALS['TL_LANG']['tl_workflow_action']['show'][0]   = 'Show details';
$GLOBALS['TL_LANG']['tl_workflow_action']['show'][1]   = 'Show details action %s ';
$GLOBALS['TL_LANG']['tl_workflow_action']['toggle'][0] = 'Activate/deactive action';
$GLOBALS['TL_LANG']['tl_workflow_action']['toggle'][1] = 'Activate/deactive action ID %s';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][0]       = 'Label';
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][1]       = 'Label of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][0]        = 'Name';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][1]        = 'Name of workflow action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][0] = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][1] = 'Description of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][0]        = 'Type';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][1]        = 'Type of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][0]       = 'Final step';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][1]       = 'Mark step as final. No more transitions will be allowed.';
$GLOBALS['TL_LANG']['tl_workflow_action']['logChanges'][0]  = 'Log changes';
$GLOBALS['TL_LANG']['tl_workflow_action']['logChanges'][1]  = 'Changes of the action get logged.';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][0]      = 'Activate action';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][1]      = 'Activated action will be executed during transition.';
