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

$GLOBALS['TL_LANG']['tl_workflow_step']['name_legend'] = 'Workflow Step';
$GLOBALS['TL_LANG']['tl_workflow_step']['permission_legend'] = 'Permission';

/*
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_workflow_step']['new'][0]    = 'Create step';
$GLOBALS['TL_LANG']['tl_workflow_step']['new'][1]    = 'Create a new step';
$GLOBALS['TL_LANG']['tl_workflow_step']['edit'][0]   = 'Edit step settings';
$GLOBALS['TL_LANG']['tl_workflow_step']['edit'][1]   = 'Edit step ID "%s" settings';
$GLOBALS['TL_LANG']['tl_workflow_step']['show'][0]   = 'Show step details';
$GLOBALS['TL_LANG']['tl_workflow_step']['show'][1]   = 'Step details';
$GLOBALS['TL_LANG']['tl_workflow_step']['delete'][0] = 'Delete step';
$GLOBALS['TL_LANG']['tl_workflow_step']['delete'][1] = 'Delete step ID "%s"';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_workflow_step']['label'][0]           = 'Label';
$GLOBALS['TL_LANG']['tl_workflow_step']['label'][1]           = 'Label of workflow step.';
$GLOBALS['TL_LANG']['tl_workflow_step']['name'][0]            = 'Name';
$GLOBALS['TL_LANG']['tl_workflow_step']['name'][1]            = 'Name of workflow step. Has to be unique in a workflow.';
$GLOBALS['TL_LANG']['tl_workflow_step']['description'][0]     = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_step']['description'][1]     = 'Description of workflow step';
$GLOBALS['TL_LANG']['tl_workflow_step']['final'][0]           = 'Final step';
$GLOBALS['TL_LANG']['tl_workflow_step']['final'][1]           = 'Mark step as final. No more transitions are allowed.';
$GLOBALS['TL_LANG']['tl_workflow_step']['limitPermission'][0] = 'Limit Permission';
$GLOBALS['TL_LANG']['tl_workflow_step']['limitPermission'][1] = 'Limit permission of the step.';
$GLOBALS['TL_LANG']['tl_workflow_step']['permission'][0]      = 'Permission';
$GLOBALS['TL_LANG']['tl_workflow_step']['permission'][1]      = 'Permission which is required to access item when it has reached this step.';
