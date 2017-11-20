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

$GLOBALS['TL_DCA']['tl_workflow_permission'] = [
    'config' => [
        'dataContainer' => 'Table',
        'sql'           => [
            'keys' => [
                'id'                          => 'primary',
                'source,source_id,permission' => 'unique',
            ],
        ],
    ],
    'fields' => [
        'id'         => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'     => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'source'     => [
            'sql' => "varchar(16) NOT NULL default ''",
        ],
        'source_id'  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'permission' => [
            'sql' => "varchar(128) NOT NULL default ''",
        ],
    ],
];
