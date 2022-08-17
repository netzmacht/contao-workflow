<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_workflow_transition'] = [
    'config' => [
        'dataContainer'   => 'Table',
        'ptable'          => 'tl_workflow',
        'ctable'          => ['tl_workflow_action'],
        'sql'             => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
        'onload_callback' => [
            ['netzmacht.contao_workflow.listeners.dca.transition', 'injectJs'],
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
                'netzmacht.contao_workflow.listeners.dca.transition',
                'generateRow',
            ],
        ],
        'label'   => [
            'fields' => ['label', 'description'],
            'format' => '<strong>%s</strong><br>%s',
        ],

        'operations' => [
            'edit'    => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'actions' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow_transition']['actions'],
                'href'            => 'table=tl_workflow_action',
                'icon'            => 'bundles/netzmachtcontaoworkflow/img/action.png',
                'button_callback' => [
                    'netzmacht.contao_workflow.listeners.dca.transition',
                    'generateActionButton',
                ],
            ],
            'delete'  => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_transition']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle'  => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow_transition']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [
                    'netzmacht.contao_toolkit.dca.listeners.state_button_callback',
                    'handleButtonCallback',
                ],
                'toolkit'         => [
                    'state_button' => ['stateColumn' => 'active'],
                ],
            ],
            'show'    => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        '__base__'                     => [
            'name'        => ['label', 'type', 'active', 'description'],
            'config'      => [],
            'conditions'  => ['addPropertyConditions', 'addExpressionConditions'],
            'permissions' => ['limitPermission'],
            'backend'     => ['icon', 'hide'],
        ],
        'default'                      => [
            'name' => ['label', 'type'],
        ],
        'actions extends __base__'     => [
            '+name' => ['stepTo'],
        ],
        'conditional extends __base__' => [
            'config' => ['conditionalTransitions', 'editAllTransitions'],
        ],
        'workflow extends __base__'    => [
            'config' => ['workflow'],
        ],
    ],

    'metasubpalettes' => [
        'limitPermission'         => ['permission'],
        'addPropertyConditions'   => ['propertyConditions'],
        'addExpressionConditions' => ['expressionConditions'],
    ],

    'fields' => [
        'id'                      => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'pid'                     => [
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
            'foreignKey' => 'tl_workflow.name',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'                  => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'sorting'                 => ['sql' => "int(10) unsigned NOT NULL default '0'"],
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
        'type'                    => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['type'],
            'inputType'        => 'select',
            'filter'           => true,
            'reference'        => &$GLOBALS['TL_LANG']['tl_workflow_transition']['types'],
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.transition',
                'getTypes',
            ],
            'exclude'          => true,
            'default'          => 'actions',
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
                'helpwizard'         => true,
            ],
            'sql'              => "varchar(64) NOT NULL default 'actions'",
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
                'mandatory'          => true,
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
        'hide'                    => [
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
                'style'        => 'max-width: 1000px',
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
                            'style'              => 'width: 100%',
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
                            'style'     => 'width: 100%',
                        ],
                    ],
                    'value'    => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['comparisonValue'],
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width: 100%'],
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
        'conditionalTransitions'  => [
            'label'         => &$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionalTransitions'],
            'legend'        => 'ABC',
            'inputType'     => 'multiColumnWizard',
            'load_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.transition', 'loadConditionalTransitions'],
            ],
            'save_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.transition', 'saveConditionalTransitions'],
            ],
            'eval'          => [
                'tl_class'       => 'clr',
                'columnFields'   => [
                    'transitions' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['transition'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.transition',
                            'getConditionalTransitions',
                        ],
                        'eval'             => [
                            'style'              => 'width: 100%',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'edit'        => [
                        'label'                => &$GLOBALS['TL_LANG']['tl_module']['merger_data_edit'],
                        'eval'                 => ['tl_class' => 'edit_conditional_transition_column'],
                        'input_field_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.transition',
                            'conditionalTransitionEditButton',
                        ],
                    ],
                ],
                'flatArray'      => true,
                'doNotSaveEmpty' => true,
                'nullIfEmpty'    => true,
            ],
        ],
        'editAllTransitions'      => [
            'input_field_callback' => [
                'netzmacht.contao_workflow.listeners.dca.transition',
                'editAllTransitionsButton',
            ],
        ],
        'workflow'                => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_transition']['workflow'],
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.transition', 'getWorkflows'],
            'eval'             => [
                'tl_class'           => 'clr w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
            ],
            'sql'              => "varchar(128) NOT NULL default ''",
        ],
    ],
];
