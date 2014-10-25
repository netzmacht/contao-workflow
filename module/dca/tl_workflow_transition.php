<?php

$GLOBALS['TL_DCA']['tl_workflow_transition'] = array
(
    'config' => array
    (
        'dataContainer' => 'Table',
        'ptable' => 'tl_workflow',
        'sql'           => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid'=>'index'
            )
        ),
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'   => 1,
            'fields' => array('name'),
        ),
        'label' => array
        (
            'fields' => array('label', 'name', 'description'),
            'format' => '<strong>%s</strong> <span class="tl_gray">[%s]</span><br>%s',
        ),

        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.gif',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    'metapalettes' => array
    (
        'default' => array
        (
            'name'    => array('name', 'label', 'description'),
            'process' => array('start', 'process'),
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
        'pid'         => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
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
                'mandatory' => true,
                'maxlength' => 64,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'label'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'description'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'clr long',
                'maxlength' => 255,
            ),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
    ),
);