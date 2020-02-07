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
$GLOBALS['TL_LANG']['tl_workflow_transition']['name_legend']        = 'Transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['permissions_legend'] = 'Permssions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['backend_legend']     = 'Backend integration';
$GLOBALS['TL_LANG']['tl_workflow_transition']['conditions_legend']  = 'Conditions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['config_legend']      = 'Config';

/*
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_workflow_transition']['new'][0]    = 'Create transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['new'][1]    = 'Create a new transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['copy'][0]   = 'Create transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['copy'][1]   = 'Create a new transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['edit'][0]   = 'Edit transition settings';
$GLOBALS['TL_LANG']['tl_workflow_transition']['edit'][1]   = 'Edit transition ID "%s" settings';
$GLOBALS['TL_LANG']['tl_workflow_transition']['show'][0]   = 'Show transition details';
$GLOBALS['TL_LANG']['tl_workflow_transition']['show'][1]   = 'transition details';
$GLOBALS['TL_LANG']['tl_workflow_transition']['delete'][0] = 'Delte transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['delete'][1] = 'Delete transition ID "%s"';
$GLOBALS['TL_LANG']['tl_workflow_transition']['toggle'][0] = 'Activate/deactive transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['toggle'][1] = 'Activate/deactive transition ID %s';

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_workflow_transition']['label'][0]                   = 'Label';
$GLOBALS['TL_LANG']['tl_workflow_transition']['label'][1]                   = 'Label of the transition.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['type'][0]                    = 'Type';
$GLOBALS['TL_LANG']['tl_workflow_transition']['type'][1]                    = 'The type specifies what the transition is supposed to do.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['description'][0]             = 'Description';
$GLOBALS['TL_LANG']['tl_workflow_transition']['description'][1]             = 'Description of the transition.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['limitPermission'][0]         = 'Limit permissions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['limitPermission'][1]         = 'Limit permission to defined roles.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['stepTo'][0]                  = 'Target';
$GLOBALS['TL_LANG']['tl_workflow_transition']['stepTo'][1]                  = 'Step which is reached after a successfull transition.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['permission'][0]              = 'Permission';
$GLOBALS['TL_LANG']['tl_workflow_transition']['permission'][1]              = 'Permission which is required to perform transition.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['hide'][0]                    = 'Hide';
$GLOBALS['TL_LANG']['tl_workflow_transition']['hide'][1]                    = 'Transition will not be visible as button in the transition view.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['icon'][0]                    = 'Icon image';
$GLOBALS['TL_LANG']['tl_workflow_transition']['icon'][1]                    = 'Please select an icon image.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['active'][0]                  = 'Activate transition';
$GLOBALS['TL_LANG']['tl_workflow_transition']['active'][1]                  = 'A transition has to be activated to be used.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['addPropertyConditions'][0]   = 'Add property conditions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['addPropertyConditions'][1]   = 'Add conditions which compare property values.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['propertyConditions'][0]      = 'Property conditions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['propertyConditions'][1]      = 'Define property conditions.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['addExpressionConditions'][0] = 'Add expression conditions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['addExpressionConditions'][1] = 'Add conditions which are defined using the Symfony Expression syntax. Provided objects are transition, item, entity, entityId, context and error collection.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['expressionConditions'][0]    = 'Expression conditions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['expressionConditions'][1]    = 'Define expression conditions.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionType'][0]           = 'Condition type';
$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionType'][1]           = 'Conditions can be pre conditions or conditions. Pre conditions are run before form is filled.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['expression'][0]              = 'Condition expression';
$GLOBALS['TL_LANG']['tl_workflow_transition']['expression'][1]              = 'Condition expression using Symfony Expression syntax.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['entityProperty'][0]          = 'Entity property';
$GLOBALS['TL_LANG']['tl_workflow_transition']['entityProperty'][1]          = 'Choose an entity property';
$GLOBALS['TL_LANG']['tl_workflow_transition']['operator'][0]                = 'Operator';
$GLOBALS['TL_LANG']['tl_workflow_transition']['operator'][1]                = 'Choose a comparison operator.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['comparisonValue'][0]         = 'Value';
$GLOBALS['TL_LANG']['tl_workflow_transition']['comparisonValue'][1]         = 'Value to compare with.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionalTransitions'][0]  = 'Transitions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionalTransitions'][1]  = 'A list of transitions to branch to when executing this action. The transitions are tested from top to bottom. The first matching condition selects the finally executed transition.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['workflow'][0]                = 'Workflow';
$GLOBALS['TL_LANG']['tl_workflow_transition']['workflow'][1]                = 'Workflow which shall be started.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['editAllTransitions']         = 'Edit transitions';

$GLOBALS['TL_LANG']['tl_workflow_transition']['eq']  = 'equals';
$GLOBALS['TL_LANG']['tl_workflow_transition']['neq'] = 'not equals';
$GLOBALS['TL_LANG']['tl_workflow_transition']['gt']  = 'greater than';
$GLOBALS['TL_LANG']['tl_workflow_transition']['gte'] = 'greater than or equals';
$GLOBALS['TL_LANG']['tl_workflow_transition']['lt']  = 'lesser than';
$GLOBALS['TL_LANG']['tl_workflow_transition']['lte'] = 'lesser than or equals';

$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['actions'][0]     = 'Default';
$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['actions'][1]     = 'The default transition executes a set of defined actions';
$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['conditional'][0] = 'Conditional';
$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['conditional'][1] = 'The conditional transition executes the first available transition of a list of defined transitions.';
$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['workflow'][0]    = 'Workflow change';
$GLOBALS['TL_LANG']['tl_workflow_transition']['types']['workflow'][1]    = 'The workflow change transition starts a new workflow';
