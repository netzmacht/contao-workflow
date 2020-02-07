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

$GLOBALS['TL_DCA']['tl_workflow_state'] = [
    'config' => [
        'dataContainer'   => 'Table',
        'closed'          => true,
        'sql'             => [
            'keys' => [
                'id'       => 'primary',
                'entityId' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'panelLayout' => 'filter;sort,limit',
            'fields'      => ['entityId', 'reachedAt DESC'],
            'mode'        => 2,
        ],
        'label'   => [
            'fields'         => ['entityId', 'workflowName', 'transitionName', 'stepName', 'success', 'reachedAt'],
            'label_callback' => ['netzmacht.contao_workflow.listeners.dca.state', 'generateRow'],
            'group_callback' => ['netzmacht.contao_workflow.listeners.dca.state', 'generateGroupHeader'],
        ],

        'operations' => [
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_state']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'fields' => [
        'id'             => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'         => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'workflowName'   => [
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'targetWorkflowName'   => [
            'sql' => "varchar(32) NULL default NULL",
        ],
        'entityId'       => [
            'sql'     => "varchar(64) NOT NULL default ''",
            'sorting' => true,
            'filter'  => true,
        ],
        'transitionName' => [
            'filter' => true,
            'sql'    => "varchar(32) NOT NULL default ''",
        ],
        'stepName'       => [
            'filter' => true,
            'sql'    => "varchar(32) NOT NULL default ''",
        ],
        'success'        => [
            'filter' => true,
            'sql'    => "char(1) NOT NULL default ''",
        ],
        'reachedAt'      => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'data'           => [
            'sql' => 'text NULL',
        ],
        'errors'         => [
            'sql' => 'text NULL',
        ],
    ],

];
