<?php

declare(strict_types=1);

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
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][0]                    = 'Label';
$GLOBALS['TL_LANG']['tl_workflow_action']['label'][1]                    = 'Label of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][0]                     = 'Name';
$GLOBALS['TL_LANG']['tl_workflow_action']['name'][1]                     = 'Name of workflow action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][0]              = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_action']['description'][1]              = 'Description of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][0]                     = 'Type';
$GLOBALS['TL_LANG']['tl_workflow_action']['type'][1]                     = 'Type of the action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][0]                    = 'Final step';
$GLOBALS['TL_LANG']['tl_workflow_action']['final'][1]                    = 'Mark step as final. No more transitions will be allowed.';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged'][0]          = 'Log changes';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged'][1]          = 'Changes of the action get logged.';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][0]                   = 'Activate action';
$GLOBALS['TL_LANG']['tl_workflow_action']['active'][1]                   = 'Activated action will be executed during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'][0]              = 'Form';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'][1]              = 'Please select a from which should be integrated.';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'][0]            = 'Add fieldset wrapper';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'][1]            = 'Add fieldset around form fields (not required if fieldsets are defined in form).';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'][0]            = 'Required';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'][1]            = 'Please define if not has to be filled.';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'][0]           = 'Min length';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'][1]           = 'If defined minimum number of characters are required.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'][0]          = 'Notification';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'][1]          = 'Please choose a notification which should be send.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'][0]      = 'Success states';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'][1]      = 'Only send notification if transition was made with success/failed.';
$GLOBALS['TL_LANG']['tl_workflow_action']['property'][0]                 = 'Property';
$GLOBALS['TL_LANG']['tl_workflow_action']['property'][1]                 = 'Select a property which should be changed';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_expression'][0]      = 'Evaluate expression';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_expression'][1]      = 'The property value is a symfony expression.';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_value'][0]           = 'Property value/expression';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_value'][1]           = 'The new property value. If an expression is used, access to <em>entity</em> and <em>now</em> is granted. Example: <em>entity.get(\'author.admin\') ? \'\' : (now.getTimestamp() + 86400)</em>';
$GLOBALS['TL_LANG']['tl_workflow_action']['update_entity_properties'][0] = 'Properties';
$GLOBALS['TL_LANG']['tl_workflow_action']['update_entity_properties'][1] = 'Please choose the properties which should be updated within this action.';
$GLOBALS['TL_LANG']['tl_workflow_action']['reference'][0]                = 'Reference';
$GLOBALS['TL_LANG']['tl_workflow_action']['reference'][1]                = 'Reference action defined on workflow level.';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_property'][0]     = 'User property';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_property'][1]     = 'Please select a property which is used for the assigned user.';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_permission'][0]   = 'Required permission';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_permission'][1]   = 'The permission is used to filter the assignable users.';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_current_user'][0] = 'Assign current user';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_current_user'][1] = 'There is no select form to assign a user but the current user is used.';
$GLOBALS['TL_LANG']['tl_workflow_action']['payload_name'][0]             = 'Payload name';
$GLOBALS['TL_LANG']['tl_workflow_action']['payload_name'][1]             = 'Name used to identify action data in the payload/state properties.';

/*
 * Values
 */
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['default']            = 'Default';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['transitions']        = 'Transitions';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note'][0]            = 'Note';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note'][1]            = 'Add a note during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['reference'][0]       = 'Reference';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['reference'][1]       = 'Reference to an action defined on workflow level.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form'][0]            = 'Form';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form'][1]            = 'Add data from a form generated with the form generator during transition.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification'][0]    = 'Notification';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification'][1]    = 'Send a notification with the Notification Center.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_property'][0] = 'Update property';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_property'][1] = 'Update property value of an entity.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_entity'][0]   = 'Update entity';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_entity'][1]   = 'Update entity by providing a form with selected properties.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['assign_user'][0]     = 'Assign user';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['assign_user'][1]     = 'Assign a user to the entity. An assigned user may have access without having a permission.';

$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['success'] = 'success';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['failed']  = 'failed';
