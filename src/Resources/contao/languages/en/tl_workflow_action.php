<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

/*
 * Legends
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['name_legend']        = 'Workflow Action';
$GLOBALS['TL_LANG']['tl_workflow_action']['description_legend'] = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_action']['config_legend']      = 'Config';
$GLOBALS['TL_LANG']['tl_workflow_action']['conditions_legend']  = 'Conditions';

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
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][0]               = 'Label';
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][1]               = 'Label of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][0]                = 'Name';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][1]                = 'Name of workflow action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][0]         = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][1]         = 'Description of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][0]                = 'Type';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][1]                = 'Type of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][0]               = 'Final step';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][1]               = 'Mark step as final. No more transitions will be allowed.';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged'][0]     = 'Log changes';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged'][1]     = 'Changes of the action get logged.';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][0]              = 'Activate action';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][1]              = 'Activated action will be executed during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'][0]         = 'Form';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'][1]         = 'Please select a from which should be integrated.';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'][0]       = 'Add fieldset wrapper';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'][1]       = 'Add fieldset around form fields (not required if fieldsets are defined in form).';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'][0]       = 'Required';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'][1]       = 'Please define if not has to be filled.';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'][0]      = 'Min length';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'][1]      = 'If defined minimum number of characters are required.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'][0]     = 'Notification';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'][1]     = 'Please choose a notification which should be send.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'][0] = 'Success states';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'][1] = 'Only send notification if transition was made with success/failed.';

/*
 * Values
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['default']         = 'Default';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note'][0]         = 'Note';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note'][1]         = 'Add a note during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form'][0]         = 'Form';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form'][1]         = 'Add data from a form generated with the form generator during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification'][0] = 'Notification';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification'][1] = 'Send a notification with the Notification Center.';

$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['success'] = 'success';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['failed']  = 'failed';
