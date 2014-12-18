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


$GLOBALS['TL_DCA']['tl_workflow_state'] = array
(
    'config' => array
    (
        'dataContainer' => 'Table',
        'closed' => true,
        'onload_callback' => array(
            array(
                'Netzmacht\Workflow\Contao\Backend\Dca\State',
                'applyFilter'
            )
        ),
        'sql'           => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'entityId' => 'index',
            )
        ),
    ),

    'list' => array(
        'sorting' => array(
            'panelLayout' => 'filter;sort,limit',
            'fields' => array('entityId', 'reachedAt DESC'),
            'mode'   => 2,
        ),
        'label' => array(
            'fields'         => array('entityId', 'workflowName', 'transitionName', 'stepName', 'success', 'reachedAt'),
            'label_callback' => array('Netzmacht\Workflow\Contao\Backend\Dca\State', 'generateRow'),
            'group_callback' => array('Netzmacht\Workflow\Contao\Backend\Dca\State', 'generateGroupHeader'),
        ),

        'operations' => array(
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_state']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
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
        'entityId'             => array
        (
            'sql' => "varchar(64) NOT NULL default ''",
            'sorting' => true,
            'filter' => true,
        ),
        'transitionName'             => array
        (
            'filter' => true,
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'stepName'             => array
        (
            'filter' => true,
            'sql' => "varchar(32) NOT NULL default ''"
        ),
        'success'             => array
        (
            'filter' => true,
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
