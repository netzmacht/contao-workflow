<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Erik Wegner <e_wegner@web.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */


$GLOBALS['TL_DCA']['tl_workflow_transition_conditional_transition'] = [
    'config' => [
        'dataContainer' => 'Table',
        'sql'           => [
            'keys' => [
                'id'      => 'primary',
                'tid,pid' => 'unique',
            ],
        ],
    ],

    'fields' => [
        'id'      => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'pid'     => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tid'     => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];
