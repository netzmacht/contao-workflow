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

$GLOBALS['TL_LANG']['tl_workflow_state']['show'][0] = 'Show details';
$GLOBALS['TL_LANG']['tl_workflow_state']['show'][1] = 'Show details';

$GLOBALS['TL_LANG']['tl_workflow_state']['entityId'][0] = 'Entity';
$GLOBALS['TL_LANG']['tl_workflow_state']['entityId'][1] = 'EntityId, combined by provider name and id.';

$GLOBALS['TL_LANG']['tl_workflow_state']['workflowName'][0] = 'Workflow';
$GLOBALS['TL_LANG']['tl_workflow_state']['workflowName'][1] = 'Name of the workflow';

$GLOBALS['TL_LANG']['tl_workflow_state']['transitionName'][0] = 'Transition';
$GLOBALS['TL_LANG']['tl_workflow_state']['transitionName'][1] = 'Last performed transition';

$GLOBALS['TL_LANG']['tl_workflow_state']['stepName'][0] = 'Current step';
$GLOBALS['TL_LANG']['tl_workflow_state']['stepName'][1] = 'Name of current step';

$GLOBALS['TL_LANG']['tl_workflow_state']['reachedAt'][0] = 'Reached at';
$GLOBALS['TL_LANG']['tl_workflow_state']['reachedAt'][1] = 'Time when step transition was performed';

$GLOBALS['TL_LANG']['tl_workflow_state']['success'][0] = 'Success';
$GLOBALS['TL_LANG']['tl_workflow_state']['success'][1] = 'Was transition successful.';
