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
            'fields' => array('sorting'),
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
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'toggle' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_transition']['toggle'],
                'icon'       => 'visible.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\DevTools\Dca::createToggleIconCallback(
                    'tl_workflow_transition',
                    'active'
                )
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
            'conditions'  => array('addPropertyConditions', 'addExpressionConditions'),
            'backend'     => array('addIcon'),
            'activation'  => array('active')
        ),
    ),

    'metasubpalettes' => array
    (
        'limitPermission'         => array('permission'),
        'addIcon'                 => array('icon'),
        'addPropertyConditions'   => array('propertyConditions'),
        'addExpressionConditions' => array('expressionConditions'),
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
        'sorting'         => array
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
                'tl_class'       => 'clr icon_selector',
                'filesOnly' => true,
                'fieldType' => 'radio',
                'extensions' => 'jpg,gif,png',
            ),
            'sql'       => "binary(16) NULL"
        ),
        'active'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['active'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'addPropertyConditions'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['addPropertyConditions'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'propertyConditions'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['propertyConditions'],
            'inputType' => 'multiColumnWizard',
            'eval'      => array(
                'tl_class'       => 'clr',
                'columnFields'   => array(
                    'property' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['entityProperty'],
                        'inputType' => 'select',
                        'options_callback' => array
                        (
                            'Netzmacht\Workflow\Contao\Backend\Dca\Transition',
                            'getEntityProperties'
                        ),
                        'eval'      => array
                        (
                            'mandatory' => true,
                            'includeBlankOption' => true,
                            'chosen' => true,
                            'style' => 'width: 200px',
                        ),
                    ),
                    'operator' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['operator'],
                        'inputType' => 'select',
                        'reference' => &$GLOBALS['TL_LANG']['tl_workflow_transition'],
                        'options' => array
                        (
                            'eq', 'lt', 'lte', 'gt', 'gte', 'neq'
                        ),
                        'eval'      => array
                        (
                            'mandatory' => true,
                            'style' => 'width: 120px',
                        ),
                    ),
                    'value' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['comparisonValue'],
                        'inputType' => 'text',
                        'eval'      => array
                        (
                            'style' => 'width: 200px',
                        ),
                    ),
                )
            ),
            'sql'       => "mediumblob NULL"
        ),
        'addExpressionConditions'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['addExpressionConditions'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'expressionConditions'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['expressionConditions'],
            'inputType' => 'multiColumnWizard',
            'eval'      => array(
                'tl_class'       => 'clr',
                'columnFields'   => array(
                    'type' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['conditionType'],
                        'inputType' => 'select',
                        'options'   => array('pre', 'con'),
                        'eval'      => array
                        (
                            'mandatory' => true,
                            'style' => 'width: 150px',
                        ),
                    ),
                    'expression' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_workflow_transition']['expression'],
                        'inputType' => 'text',
                        'eval'      => array
                        (
                            'style' => 'width: 400px',
                            'preserveTags' => true,
                            'allowHtml' => true,
                        ),
                    ),
                )
            ),
            'sql'       => "mediumblob NULL"
        ),
    ),
);
