<?php

$GLOBALS['TL_DCA']['tl_workflow'] = array
(
    'config' => array
    (
        'dataContainer' => 'Table',
        'sql'           => array
        (
            'keys' => array
            (
                'id' => 'primary',
            )
        ),
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'   => 1,
            'fields' => array('type', 'name'),
        ),
        'label' => array
        (
            'fields' => array('name', 'type', 'description'),
            'format' => '<strong>%s <span class="tl_gray">%s</span></strong><br>%s',
        ),
    ),

    'metapalettes' => array
    (
        'default' => array
        (
            'name'    => array('name', 'type'),
            'config'  => array(),
            'publish' => array('published')
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
        'name'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'type'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['type'],
            'inputType' => 'select',
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Workflow',
                'getTypes'
            ),
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'published'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['published'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
    ),

);