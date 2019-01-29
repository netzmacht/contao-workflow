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

$GLOBALS['TL_DCA']['tl_workflow'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ctable'        => [
            'tl_workflow_step',
            'tl_workflow_transition',
            'tl_workflow_action'
        ],
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_workflow.listeners.dca.workflow', 'saveProviderName']
        ],
    ],

    'list' => [
        'sorting' => [
            'mode'        => 1,
            'fields'      => ['type'],
            'panelLayout' => 'sorting,filter;search',
        ],
        'label'   => [
            'fields'         => ['type', 'description'],
            'label_callback' => ['netzmacht.contao_workflow.listeners.dca.common', 'generateRow'],
        ],

        'operations' => [
            'edit'        => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'actions' => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['actions'],
                'href'  => 'table=tl_workflow_action',
                'icon'  => 'bundles/netzmachtcontaoworkflow/img/action.png',
            ],
            'steps'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['steps'],
                'href'  => 'table=tl_workflow_step',
                'icon'  => 'bundles/netzmachtcontaoworkflow/img/step.png',
            ],
            'transitions' => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['transitions'],
                'href'  => 'table=tl_workflow_transition',
                'icon'  => 'bundles/netzmachtcontaoworkflow/img/transition.png',
            ],
            'delete'      => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle'      => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow']['toggle'],
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
            'show'        => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['show'],
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
            'name'        => ['label', 'type', 'providerName', 'active'],
            'description' => [':hide', 'description'],
            'permissions' => ['permissions'],
            'process'     => ['process'],
            'config'      => [],
        ],
    ],

    'fields' => [
        'id'           => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'       => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'label'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'maxlength' => 64,
                'mandatory' => true,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'type'         => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['type'],
            'inputType'        => 'select',
            'filter'           => true,
            'reference'        => &$GLOBALS['TL_LANG']['workflow']['types'],
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.workflow',
                'getTypes',
            ],
            'exclude'          => true,
            'eval'             => [
                'tl_class'       => 'w50',
                'mandatory'      => true,
                'submitOnChange' => true,
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'providerName' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['providerName'],
            'inputType'        => 'select',
            'search'           => true,
            'exclude'          => true,
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.workflow',
                'getProviderNames',
            ],
            'eval'             => [
                'tl_class'  => 'w50',
                'mandatory' => true,
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'description'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class' => 'clr long',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'process'      => [
            'label'         => &$GLOBALS['TL_LANG']['tl_workflow']['process'],
            'inputType'     => 'multiColumnWizard',
            'exclude'       => true,
            'save_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.workflow', 'validateProcess'],
            ],
            'eval'          => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'step'       => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['step'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.workflow',
                            'getStartSteps',
                        ],
                        'reference'        => &$GLOBALS['TL_LANG']['tl_workflow']['process'],
                        'eval'             => [
                            'style'              => 'width:200px',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                        ],
                    ],
                    'transition' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['transition'],
                        'inputType'        => 'select',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.workflow',
                            'getTransitions',
                        ],
                        'eval'             => [
                            'style'              => 'width:400px',
                            'includeBlankOption' => true,
                            'chosen'             => true,
                        ],
                    ],
                ],
            ],
            'sql'           => 'mediumblob NULL',
        ],
        'permissions'  => [
            'label'         => &$GLOBALS['TL_LANG']['tl_workflow']['permissions'],
            'inputType'     => 'multiColumnWizard',
            'exclude'       => true,
            'save_callback' => [
                ['netzmacht.contao_workflow.listeners.dca.workflow', 'validatePermissions'],
            ],
            'eval'          => [
                'tl_class'     => 'clr',
                'columnFields' => [
                    'label' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['permission_label'],
                        'inputType'        => 'text',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.workflow',
                            'getStartSteps',
                        ],
                        'eval'             => [
                            'style' => 'width:350px',
                        ],
                    ],
                    'name'  => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_workflow']['permission_name'],
                        'inputType'        => 'text',
                        'options_callback' => [
                            'netzmacht.contao_workflow.listeners.dca.workflow',
                            'getStartSteps',
                        ],
                        'eval'             => [
                            'style' => 'width:230px',
                        ],
                    ],
                    'guest' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['permission_guest'],
                        'inputType' => 'checkbox',
                        'eval'      => [
                            'style' => 'width:80px',
                        ],
                    ],
                ],
            ],
            'sql'           => 'mediumblob NULL',
        ],
        'active'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['active'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
    ],
];
