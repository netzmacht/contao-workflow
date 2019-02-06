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

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_workflow_action']['types']['example']                 = 'Example';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['example_publish'][0]      = 'Change publish state';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['example_notification'][0] = 'Send notification';

$GLOBALS['TL_LANG']['tl_workflow_action']['publish_state'][0]          = 'Published';
$GLOBALS['TL_LANG']['tl_workflow_action']['publish_state'][1]          = 'Please define new published state.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_recipient'][0] = 'Reciepient';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_recipient'][1] = 'Please insert e-mail address of recipient.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_message'][0]   = 'Message';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_message'][1]   = 'E-Mail message.';
