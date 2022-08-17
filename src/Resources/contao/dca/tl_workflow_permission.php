<?php

declare(strict_types=1);

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
        'id'         => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp'     => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'source'     => ['sql' => "varchar(16) NOT NULL default ''"],
        'source_id'  => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'permission' => ['sql' => "varchar(128) NOT NULL default ''"],
    ],
];
