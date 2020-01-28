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


$GLOBALS['TL_DCA']['tl_workflow_transition'] = [
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
            'flag'                  => 1,
            'headerFields'          => ['label', 'type', 'description'],
            'fields'                => ['sorting'],
            'disableGrouping'       => true,
            'child_record_callback' => [
                'netzmacht.contao_workflow.listeners.dca.common',
                'generateRow',
            ],
        ],
        'label'   => [
            'fields' => ['label', 'description'],
            'format' => '<strong>%s</strong><br>%s',
        ],

        'operations' => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_transition']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow_transition']['toggle'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'metapalettes' => [
        'default' => [
            'name'                   => ['label', 'active', 'description', 'stepTo'],
            'config'                 => [],
            'actions'                => ['actions'],
            'permissions'            => ['limitPermission'],
            'conditions'             => ['addPropertyConditions', 'addExpressionConditions'],
            'backend'                => ['icon', 'hide'],
        ],
    ],

    'metasubpalettes' => [
        'limitPermission'         => ['permission'],
        'addPropertyConditions'   => ['propertyConditions'],
        'addExpressionConditions' => ['expressionConditions'],
    ],

    'fields' => [
        'id'                      => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'                     => [
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
            'foreignKey' => 'tl_workflow.name',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'                  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting'                 => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'label'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'description'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'clr long',
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'stepTo'                  => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['stepTo'],
            'inputType'        => 'select',
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.transition',
                'getStepsTo',
            ],
            'eval'             => [
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'relation'         => [
                'type'  => 'hasOne',
                'table' => 'tl_workflow_step',
                'load'  => 'eager',
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'limitPermission'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['limitPermission'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50 m12',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'permission'              => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['permission'],
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.permissions', 'getWorkflowPermissions'],
            'eval'             => [
                'tl_class'  => 'w50',
                'mandatory' => true,

            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'icon'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['icon'],
            'inputType' => 'fileTree',
            'eval'      => [
                'tl_class'   => 'clr icon_selector',
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'extensions' => 'jpg,gif,png,svg',
            ],
            'sql'       => 'binary(16) NULL',
        ],
        'hide'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['hide'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50 m12',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'active'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['active'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'actions'                 => [
            'label'         => &$GLOBALS['TL_LANG']['tl_workflow_transition']['actions'],
            'inputType'     => 'multiColumnWizard',
            'load_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.transition', 'loadRelatedActions'],
            ],
            'save_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.transition', 'saveRelatedActions'],
            ],
            'eval'          => [
                'tl_class'       => 'clr',
                'columnFields'   => [
                    'action' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['action'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.transition',
                            'getActions',
                        ],
                        'eval'             => [
                            'style'              => 'width: 100%',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                ],
                'flatArray'      => true,
                'doNotSaveEmpty' => true,
                'nullIfEmpty'    => true,
            ],
        ],
        'addPropertyConditions'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['addPropertyConditions'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'propertyConditions'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['propertyConditions'],
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'property' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['entityProperty'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.transition',
                            'getEntityProperties',
                        ],
                        'eval'             => [
                            'mandatory'          => true,
                            'includeBlankOption' => true,
                            'chosen'             => true,
                            'style'              => 'width: 200px',
                        ],
                    ],
                    'operator' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['operator'],
                        'inputType' => 'select',
                        'reference' => &$GLOBALS['TL_LANG']['tl_workflow_transition'],
                        'options'   => [
                            'eq',
                            'lt',
                            'lte',
                            'gt',
                            'gte',
                            'neq',
                        ],
                        'eval'      => [
                            'mandatory' => true,
                            'style'     => 'width: 120px',
                        ],
                    ],
                    'value'    => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['comparisonValue'],
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width: 200px',
                        ],
                    ],
                ],
            ],
            'sql'       => 'mediumblob NULL',
        ],
        'addExpressionConditions' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['addExpressionConditions'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'expressionConditions'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['expressionConditions'],
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'type'       => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionType'],
                        'inputType' => 'select',
                        'options'   => ['pre', 'con'],
                        'eval'      => [
                            'mandatory' => true,
                            'style'     => 'width: 150px',
                        ],
                    ],
                    'expression' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['expression'],
                        'inputType' => 'text',
                        'eval'      => [
                            'style'        => 'width: 400px',
                            'preserveTags' => true,
                            'allowHtml'    => true,
                        ],
                    ],
                ],
            ],
            'sql'       => 'mediumblob NULL',
        ],
    ],
];
