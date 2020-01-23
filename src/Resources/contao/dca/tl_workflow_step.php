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


$GLOBALS['TL_DCA']['tl_workflow_step'] = [
    'config' => [
        'dataContainer'   => 'Table',
        'ptable'          => 'tl_workflow',
        'sql'             => [
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
            'fields'                => ['label'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_step']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_step']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_step']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['limitPermission'],
    ],

    'metapalettes' => [
        'default' => [
            'name'       => ['label', 'description', 'final', 'triggerWorkflow'],
            'permission' => ['limitPermission'],
        ],
    ],

    'metasubpalettes' => [
        'limitPermission' => ['permission'],
    ],

    'fields' => [
        'id'              => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'             => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'          => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'label'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_step']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'description'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_step']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'clr long',
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'final'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_step']['final'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'triggerWorkflow' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_step']['triggerWorkflow'],
            'inputType' => 'select',
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.step', 'getWorkflows'],
            'eval'             => [
                'tl_class'  => 'clr w50',
                'mandatory' => false,
                'includeBlankOption' => true,
            ],
            'sql'              => "int(10) unsigned",
        ],
        'limitPermission' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_step']['limitPermission'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class'       => 'clr w50 m12',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'permission'      => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_step']['permission'],
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.permissions', 'getWorkflowPermissions'],
            'eval'             => [
                'tl_class'  => 'w50',
                'mandatory' => true,
            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
    ],
];
