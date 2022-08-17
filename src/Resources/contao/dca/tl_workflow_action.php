<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_workflow_action'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_workflow',
        'dynamicPtable' => true,
        'sql'           => [
            'keys' => [
                'id'                 => 'primary',
                'pid,ptable,sorting' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode'                  => 4,
            'flag'                  => 1,
            'fields'                => ['sorting'],
            'headerFields'          => ['label', 'type', 'description'],
            'child_record_callback' => [
                'netzmacht.contao_workflow.listeners.dca.action',
                'generateRow',
            ],
            'panelLayout'           => 'filter;search,limit',
        ],
        'label'   => [
            'fields' => ['label', 'type'],
        ],

        'operations' => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_action']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_workflow_action']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_workflow_action']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [
                    'netzmacht.contao_toolkit.dca.listeners.state_button_callback',
                    'handleButtonCallback',
                ],
                'toolkit'         => [
                    'state_button' => ['stateColumn' => 'active'],
                ],
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_workflow_action']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        'default'                         => [
            'name'        => ['label', 'type', 'active'],
            'description' => [':hide', 'description'],
            'config'      => [],
        ],
        'form extends default'            => [
            'config' => ['form_formId', 'form_fieldset'],
        ],
        'note extends default'            => [
            'config' => ['note_required', 'payload_name'],
        ],
        'notification extends default'    => [
            'config' => ['notification_id', 'notification_states'],
        ],
        'update_property extends default' => [
            'config' => ['property', 'property_expression', 'property_value'],
        ],
        'update_entity extends default'   => [
            'config' => ['update_entity_properties'],
        ],
        'reference'                       => [
            'name' => ['reference', 'type', 'active'],
        ],
        'assign_user extends default'     => [
            'config' => ['assign_user_property', 'assign_user_permission', 'assign_user_current_user'],
        ],
    ],

    'metasubpalettes' => [
        'note_required' => ['note_minlength'],
    ],

    'fields' => [
        'id'                       => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'pid'                      => [
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
            'foreignKey' => 'tl_workflow_transition.label',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'ptable'                   => ['sql' => "varchar(64) NOT NULL default ''"],
        'sorting'                  => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp'                   => ['sql' => "int(10) unsigned NOT NULL default '0'"],
        'label'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['label'],
            'inputType' => 'text',
            'exclude'   => true,
            'flag'      => 1,
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 64,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'type'                     => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['type'],
            'inputType'        => 'select',
            'exclude'          => true,
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'getTypes'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_workflow_action']['types'],
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'description'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['description'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'clr long',
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'active'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['active'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'payload_name'                     => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['payload_name'],
            'inputType'        => 'text',
            'exclude'          => true,
            'eval'             => ['tl_class' => 'w50'],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'form_formId'              => [
            'label'      => &$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId'],
            'inputType'  => 'select',
            'exclude'    => true,
            'eval'       => ['tl_class' => 'w50'],
            'foreignKey' => 'tl_form.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
        ],
        'form_fieldset'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'default'   => '1',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'note_required'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['note_required'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => [
                'tl_class'       => 'clr w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'note_minlength'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength'],
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'tl_class'  => 'w50',
                'maxlength' => '3',
                'rgxp'      => 'natural',
            ],
            'sql'       => "int(3) NOT NULL default '0'",
        ],
        'notification_id'          => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id'],
            'inputType'        => 'select',
            'filter'           => true,
            'reference'        => &$GLOBALS['TL_LANG']['workflow']['types'],
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'notificationOptions'],
            'exclude'          => true,
            'eval'             => [
                'tl_class'           => 'w50',
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
            ],
            'sql'              => "int(10) NOT NULL default '0'",
        ],
        'notification_states'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states'],
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options'],
            'options'   => ['success', 'failed'],
            'exclude'   => true,
            'eval'      => [
                'tl_class' => 'clr',
                'multiple' => true,
            ],
            'sql'       => 'tinyblob NULL',
        ],
        'property'                 => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['property'],
            'inputType'        => 'select',
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.action',
                'getEntityProperties',
            ],
            'eval'             => [
                'mandatory'          => true,
                'includeBlankOption' => true,
                'chosen'             => true,
                'tl_class'           => 'w50',
            ],
            'sql'              => "varchar(255) NOT NULL default ''",
        ],
        'property_expression'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['property_expression'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'default'   => '1',
            'eval'      => [
                'tl_class'       => 'm12 w50',
                'submitOnChange' => true,
            ],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'property_value'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['property_value'],
            'inputType' => 'textarea',
            'eval'      => [
                'chosen'            => true,
                'tl_class'          => 'clr',
                'useRawRequestData' => true,
            ],
            'sql'       => 'tinytext NULL',
        ],
        'update_entity_properties' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['update_entity_properties'],
            'inputType'        => 'checkboxWizard',
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.action',
                'getEditableEntityProperties',
            ],
            'eval'             => [
                'mandatory' => true,
                'multiple'  => true,
                'tl_class'  => 'clr long',
            ],
            'sql'              => 'blob NULL',
        ],
        'reference'                => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['reference'],
            'inputType'        => 'select',
            'exclude'          => true,
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.action',
                'getWorkflowActions',
            ],
            'eval'             => [
                'tl_class'  => 'w50',
                'mandatory' => true,
            ],
            'foreignKey'       => 'tl_workflow_action.label',
            'relation'         => ['type' => 'hasOne'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'assign_user_current_user' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_workflow_action']['assign_current_user'],
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'assign_user_property'     => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_property'],
            'inputType'        => 'select',
            'options_callback' => [
                'netzmacht.contao_workflow.listeners.dca.action',
                'getUserAssignProperties',
            ],
            'eval'             => [
                'mandatory' => true,
                'multiple'  => false,
                'tl_class'  => 'clr w50',
            ],
            'sql'              => 'blob NULL',
        ],
        'assign_user_permission'   => [
            'label'            => &$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_permission'],
            'inputType'        => 'select',
            'exclude'          => true,
            'options_callback' => ['netzmacht.contao_workflow.listeners.dca.action', 'getWorkflowPermissions'],
            'eval'             => [
                'tl_class'           => 'w50',
                'multiple'           => false,
                'includeBlankOption' => true,
            ],
            'sql'              => 'varchar(32) NOT NULL default \'\'',
        ],
    ],
];
