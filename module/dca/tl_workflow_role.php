<?php

$GLOBALS['TL_DCA']['tl_workflow_role'] = array
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
            'fields' => array('name'),
            'headerFields' => array('name', 'type', 'description'),
            'disableGrouping' => true,
            'child_record_callback' => array(
                'Netzmacht\Contao\Workflow\Contao\Dca\Common',
                'generateRow'
            )
        ),
        'label' => array
        (
            'fields' => array('name'),
            'format' => '%s',
        ),

        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_role']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_role']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.gif',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_role']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    'metapalettes' => array
    (
        'default' => array
        (
            'name'       => array('label', 'name'),
            'user'       => array('users', 'userGroups'),
            'member'     => array('members', 'memberGroups'),
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
        'label'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'name'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'save_callback' => array(
                array('Netzmacht\Contao\Workflow\Contao\Dca\Common', 'createName'),
            ),
            'eval'      => array(
                'tl_class'           => 'w50',
                'maxlength' => 64,
            ),
            'sql'       => "varchar(64) NOT NULL default ''",
        ),
        'description'           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => array(
                'tl_class'           => 'clr long',
                'maxlength' => 255,
            ),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'users' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['roles'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Role',
                'getUsers',
            ),
            'eval'      => array(
                'tl_class'           => 'clr',
                'multiple' => true,
                'tl_style' => 'height:auto;',
            ),
            'sql'       => "mediumblob NULL",
        ),
        'userGroups' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['userGroups'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Role',
                'getUserGroups',
            ),
            'eval'      => array(
                'tl_class'           => 'clr',
                'multiple' => true,
                'tl_style' => 'height:auto;',
            ),
            'sql'       => "mediumblob NULL",
        ),
        'members' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['roles'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Role',
                'getMembers',
            ),
            'eval'      => array(
                'tl_class'           => 'clr',
                'multiple' => true,
                'tl_style' => 'height:auto;',
            ),
            'sql'       => "mediumblob NULL",
        ),
        'memberGroups' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_role']['memberGroups'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'options_callback' => array
            (
                'Netzmacht\Contao\Workflow\Contao\Dca\Role',
                'getMemberGroups',
            ),
            'eval'      => array(
                'tl_class'           => 'clr',
                'multiple' => true,
                'tl_style' => 'height:auto;',
            ),
            'sql'       => "mediumblob NULL",
        ),
    ),
);
