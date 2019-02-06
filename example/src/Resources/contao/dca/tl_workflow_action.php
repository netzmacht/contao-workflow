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

$GLOBALS['TL_DCA']['tl_workflow_action']['metapalettes']['example_publish extends default'] = [
    'config' => ['publish_state'],
];

$GLOBALS['TL_DCA']['tl_workflow_action']['metapalettes']['example_notification extends default'] = [
    'config' => ['notification_recipient', 'notification_message'],
];


$GLOBALS['TL_DCA']['tl_workflow_action']['fields']['publish_state'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['publish_state'],
    'inputType' => 'checkbox',
    'exclude'   => true,
    'reference' => &$GLOBALS['TL_LANG']['tl_workflow_action']['publish_state'],
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_workflow_action']['fields']['notification_recipient'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_recipient'],
    'inputType' => 'text',
    'exclude'   => true,
    'eval'      => [
        'tl_class'  => 'w50',
        'maxlength' => 255,
        'rgxp'      => 'email',
        'mandatory' => true,
    ],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_workflow_action']['fields']['notification_message'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_message'],
    'inputType' => 'textarea',
    'exclude'   => true,
    'eval'      => [
        'tl_class' => 'long clr',
    ],
    'sql'       => 'tinytext NULL',
];
