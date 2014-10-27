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
                'id'  => 'primary',
                'pid' => 'index'
            )
        ),
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'   => 4,
            'flag'   => 1,
            'headerFields' => array('name', 'type', 'description'),
            'fields' => array('name'),
            'disableGrouping' => true,
            'child_record_callback' => array(
                'Netzmacht\Contao\Workflow\Contao\Dca\Common',
                'generateRow'
            )
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
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'actions' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['actions'],
                'href'  => 'table=tl_workflow_action',
                'icon'  => 'system/modules/workflow/assets/img/action.png',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.gif',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_transition']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    'metapalettes' => array
    (
        'default' => array
        (
            'name'        => array('label', 'name', 'description'),
            'config'      => array(),
            'permissions' => array('limitPermissions')
        ),
    ),

    'metasubpalettes' => array
    (
        'limitPermissions' => array('roles'),

    ),

    'fields' => array
    (
        'id'             => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'         => array
        (
            'relation' => array('type' => 'hasOne', 'load' => 'lazy'),
            'foreignKey' => 'tl_workflow.name',
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp'         => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'name'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'save_callback' => array(
                array('Netzmacht\Contao\Workflow\Contao\Dca\Common', 'createName'),
            ),
            'eval'      => array(
                'tl_class'  => 'w50',
                'maxlength' => 64,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'label'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['label'],
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
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'clr long',
                'maxlength' => 255,
            ),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'limitPermissions'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['limitPermissions'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'roles' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['roles'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Transition',
                'getUserRoles',
            ),
            'eval'      => array(
                'tl_class'           => 'clr',
                'multiple' => true,
                'chosen'   => true,
            ),
            'sql'       => "mediumblob NULL",
        )
    ),
);
