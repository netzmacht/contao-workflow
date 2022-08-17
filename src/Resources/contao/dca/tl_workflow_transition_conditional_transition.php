<?php

declare(strict_types=1);

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
        'id'      => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp'  => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'sorting' => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'pid'     => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'tid'     => ['sql' => "int(10) unsigned NOT NULL default '0'"],
    ],
];
