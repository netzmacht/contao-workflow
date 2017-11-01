<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_workflow_permission'] = array(
    'config' => array
    (
        'dataContainer' => 'Table',
        'sql'           => array
        (
            'keys' => array
            (
                'id'                          => 'primary',
                'source,source_id,permission' => 'unique',
            )
        ),
    ),
    'fields' => array
    (
        'id'     => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'source' => array
        (
            'sql' => "varchar(16) NOT NULL default ''"
        ),
        'source_id' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'permission' => array
        (
            'sql' => "varchar(128) NOT NULL default ''"
        ),
    )
);
