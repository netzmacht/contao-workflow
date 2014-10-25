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
            'name'    => array('name', 'type', 'description'),
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
                'mandatory' => true,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'type'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['type'],
            'inputType' => 'select',
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Backend\Dca\Workflow',
                'getTypes'
            ),
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
                'mandatory' => true,
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
            ),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'process'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['process'],
            'inputType' => 'multiColumnWizard',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'clr',
                'columnFields' => array
                (
                    'step' => array
                    (
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['step'],
                        'inputType' => 'select',
                        'options_callback' => array
                        (
                            'Netzmacht\Contao\Workflow\Backend\Dca\Workflow',
                            'getSteps'
                        ),
                        'eval'      => array
                        (
                            'style' => 'width:200px'
                        ),
                    ),
                    'transition' => array
                    (
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow']['transition'],
                        'inputType' => 'select',
                        'options_callback' => array
                        (
                            'Netzmacht\Contao\Workflow\Backend\Dca\Workflow',
                            'getSteps'
                        ),
                        'eval'      => array
                        (
                            'style' => 'width:200px'
                        ),
                    ),
                )
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