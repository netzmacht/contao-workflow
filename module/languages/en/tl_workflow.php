<?php

/*
 * Legends
 */
$GLOBALS['TL_LANG']['tl_workflow']['name_legend']        = 'Workflow';
$GLOBALS['TL_LANG']['tl_workflow']['process_legend']     = 'Process definition';
$GLOBALS['TL_LANG']['tl_workflow']['permissions_legend'] = 'Permissions';
$GLOBALS['TL_LANG']['tl_workflow']['activation_legend']  = 'Activation';
$GLOBALS['TL_LANG']['tl_workflow']['description_legend'] = 'Description';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_workflow']['process'][0]          = 'Workflow process';
$GLOBALS['TL_LANG']['tl_workflow']['process'][1]          = 'Please define transitions between one step to another. There has to be exactly one start transition.';
$GLOBALS['TL_LANG']['tl_workflow']['name'][0]             = 'Name';
$GLOBALS['TL_LANG']['tl_workflow']['name'][1]             = 'Please define a workflow name.';
$GLOBALS['TL_LANG']['tl_workflow']['type'][0]             = 'Type';
$GLOBALS['TL_LANG']['tl_workflow']['type'][1]             = 'Please choose a workflow type.';
$GLOBALS['TL_LANG']['tl_workflow']['providerName'][0]     = 'Provider name';
$GLOBALS['TL_LANG']['tl_workflow']['providerName'][1]     = 'Please choose the name of the provider. Usually it\'s the table name';
$GLOBALS['TL_LANG']['tl_workflow']['description'][0]      = 'Description';
$GLOBALS['TL_LANG']['tl_workflow']['description'][1]      = 'You can use a workflow description for the backend view.';
$GLOBALS['TL_LANG']['tl_workflow']['active'][0]           = 'Active';
$GLOBALS['TL_LANG']['tl_workflow']['active'][1]           = 'Activate the workflow.';
$GLOBALS['TL_LANG']['tl_workflow']['step'][0]             = 'Step';
$GLOBALS['TL_LANG']['tl_workflow']['step'][1]             = 'Step from which the transition starts.';
$GLOBALS['TL_LANG']['tl_workflow']['transition'][0]       = 'Transition';
$GLOBALS['TL_LANG']['tl_workflow']['transition'][1]       = 'Execute the transition';
$GLOBALS['TL_LANG']['tl_workflow']['stepTo'][0]           = 'Target';
$GLOBALS['TL_LANG']['tl_workflow']['stepTo'][1]           = 'Step which is reached after transition.';
$GLOBALS['TL_LANG']['tl_workflow']['permissions'][0]      = 'Permissions';
$GLOBALS['TL_LANG']['tl_workflow']['permissions'][1]      = 'Define Workflow specific permissions here. They can be assigned to users, user groups and member groups.';
$GLOBALS['TL_LANG']['tl_workflow']['permission_label'][0] = 'Label';
$GLOBALS['TL_LANG']['tl_workflow']['permission_label'][1] = 'Visible label of the permission.';
$GLOBALS['TL_LANG']['tl_workflow']['permission_name'][0]  = 'Name';
$GLOBALS['TL_LANG']['tl_workflow']['permission_name'][1]  = 'Name of the permission has to be unique in the workflow. Changing a name will break the assignments!';

/*
 * Values
 */
$GLOBALS['TL_LANG']['tl_workflow']['process']['steps']   = 'Steps';
$GLOBALS['TL_LANG']['tl_workflow']['process']['process'] = 'Process';
$GLOBALS['TL_LANG']['tl_workflow']['process']['start']   = 'Start point';

/*
 * Workflow types
 */
$GLOBALS['TL_LANG']['workflow_type']['default'][0] = 'Default';
$GLOBALS['TL_LANG']['workflow_type']['default'][1] = 'Default workflow type';
