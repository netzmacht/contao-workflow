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

$GLOBALS['TL_DCA']['tl_workflow_action'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_workflow',
        'sql'           => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode'                  => 4,
            'flag'                  => 11,
            'fields'                => ['label'],
            'headerFields'          => ['label', 'type', 'description'],
            'child_record_callback' => [
                'netzmacht.contao_workflow.listeners.dca.common',
                'generateRow',
            ],
            'panelLayout'           => 'filter;search,limit',
        ],
        'label'   => [
            'fields' => ['label', 'type'],
        ],

        'operations' => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_action']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_action']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow_action']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [
                    'netzmacht.contao_toolkit.dca.listeners.state_button_callback',
                    'handleButtonCallback',
                ],
                'toolkit'         => [
                    'state_button' => [
                        'stateColumn' => 'active',
                    ],
                ],
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_action']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        'default'                      => [
            'name'        => ['label', 'type', 'sorting', 'active'],
            'description' => [':hide', 'description'],
            'config'      => [],
        ],
        'form extends default'         => [
            'config' => ['form_formId', 'form_fieldset'],
        ],
        'note extends default'         => [
            'config' => ['note_required'],
        ],
        'notification extends default' => [
            'config' => ['notification_id', 'notification_states'],
        ],
    ],

    'metasubpalettes' => [
        'note_required' => ['note_minlength'],
    ],

    'fields' => [
        'id'                  => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'                 => [
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
            'foreignKey' => 'tl_workflow_transition.label',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'              => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'label'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'flag'      => 1,
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'type'                => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['type'],
            'inputType'        => 'select',
            'exclude'          => true,
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'getTypes'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_workflow_action']['types'],
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'description'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'clr long',
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'active'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['active'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'form_formId'         => [
            'label'      => &$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'],
            'inputType'  => 'select',
            'exclude'    => true,
            'eval'       => [
                'tl_class' => 'w50',
            ],
            'foreignKey' => 'tl_form.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'form_fieldset'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'default'   => '1',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'note_required'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => [
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'note_minlength'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'maxlength' => '3',
                'rgxp'      => 'natural',
            ],
            'sql'       => "int(3) NOT NULL default '0'",
        ],
        'notification_id'     => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'],
            'inputType'        => 'select',
            'filter'           => true,
            'reference'        => &$GLOBALS['TL_LANG']['workflow']['types'],
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'notificationOptions'],
            'exclude'          => true,
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'sql'              => "int(10) NOT NULL default '0'",
        ],
        'notification_states' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'],
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options'],
            'options'   => ['success', 'failed'],
            'exclude'   => true,
            'eval'      => [
                'tl_class' => 'clr',
                'multiple' => true,
            ],
            'sql'       => 'tinyblob NULL',
        ],
    ],
];
