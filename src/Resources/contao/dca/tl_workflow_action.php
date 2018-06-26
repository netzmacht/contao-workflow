<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */


$GLOBALS['TL_DCA']['tl_workflow_action'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_workflow_transition',
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
            'flag'                  => 1,
            'fields'                => ['type', 'label'],
            'headerFields'          => ['label', 'type', 'description'],
            'child_record_callback' => [
                'netzmacht.contao_workflow.listeners.dca.common',
                'generateRow',
            ],
            'panelLayout'           => 'filter;search,limit',
        ],
        'label'   => [
            'fields' => ['label', 'type'],
            'format' => '%s <span class="tl_gray">[%s]</span>',
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
        'default' => [
            'name'        => ['label', 'type', 'sorting', 'active', 'postAction'],
            'description' => [':hide', 'description'],
            'config'      => [],
        ],
        'form extends default' => [
            'config' => ['formId']
        ]
    ],

    'fields' => [
        'id'          => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'         => [
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
            'foreignKey' => 'tl_workflow_transition.label',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'      => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'label'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'type'        => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['type'],
            'inputType'        => 'select',
            'exclude'          => true,
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'getTypes'],
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'description' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'clr long',
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'postAction'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['postAction'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'filter'    => true,
            'eval'      => [
                'tl_class' => 'w50 m12',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'formId'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['formId'],
            'inputType' => 'select',
            'exclude'   => true,
            'eval'      => [
                'tl_class' => 'w50',
            ],
            'foreignKey' => 'tl_form.title',
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ],
        'active'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['active'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
    ],
];
