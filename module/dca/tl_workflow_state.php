<?php

$GLOBALS['TL_DCA']['tl_workflow_state'] = array
(
    'config' => array
    (
        'dataContainer' => 'Table',
        'sql'           => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'providerName,entityId' => 'index',
            )
        ),
    ),

    'fields' => array
    (
        'id'             => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp'         => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'workflowName' => array
        (
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'providerName'             => array
        (
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'entityId'             => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'transitionName'             => array
        (
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'stepName'             => array
        (
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'success'             => array
        (
            'sql' => "char(1) NOT NULL default ''"
        ),
        'reachedAt'         => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'data'         => array
        (
            'sql' => "text NULL"
        ),
        'errors'         => array
        (
            'sql' => "text NULL"
        ),
    )

);
