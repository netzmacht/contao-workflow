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
                'Netzmacht\Workflow\Contao\Backend\Common',
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
            'name'        => array('label', 'name', 'description', 'stepTo'),
            'config'      => array(),
            'permissions' => array('limitPermission'),
            'backend'     => array('addIcon'),
        ),
    ),

    'metasubpalettes' => array
    (
        'limitPermission' => array('permission'),
        'addIcon'         => array('icon'),
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
                array('Netzmacht\Workflow\Contao\Backend\Common', 'createName'),
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
        'stepTo' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['stepTo'],
            'inputType' => 'select',
            'options_callback' => array
            (
                'Netzmacht\Workflow\Contao\Backend\Dca\Transition',
                'getStepsTo'
            ),
            'eval'      => array
            (
                'mandatory' => true,
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'chosen' => true,
            ),
            'relation' => array(
                'type' => 'hasOne',
                'table'    => 'tl_workflow_step',
                'load'     => 'eager'
            ),
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'limitPermission'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['limitPermission'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50 m12',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'permission'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['permission'],
            'inputType' => 'select',
            'options_callback' => array('Netzmacht\Workflow\Contao\Backend\Permission', 'getWorkflowPermissions'),
            'eval'      => array(
                'tl_class'       => 'w50',
                'mandatory' => true,

            ),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'addIcon'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['addIcon'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50 m12',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'icon' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['icon'],
            'inputType' => 'fileTree',
            'eval'      => array(
                'tl_class'       => 'clr',
                'filesOnly' => true,
                'fieldType' => 'radio',
                'extensions' => 'jpg,gif,png',
            ),
            'sql'       => "binary(16) NULL"
        )
    ),
);
