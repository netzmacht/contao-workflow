<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

use Netzmacht\ContaoWorkflowBundle\EventListener\Integration\OperationListener;
use Netzmacht\ContaoWorkflowBundle\EventListener\Integration\OptionsListener;
use Netzmacht\ContaoWorkflowBundle\EventListener\Integration\SubmitButtonsListener;

$GLOBALS['TL_DCA']['tl_example'] = [
    // Config
    'config'   => [
        'dataContainer' => 'Table',
        'switchToEdit'  => true,
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [
            [SubmitButtonsListener::class, 'redirectToTransition']
        ]
    ],

    // Edit
    'edit' => [
        'buttons_callback' => [
            [SubmitButtonsListener::class, 'addTransitionButtons']
        ]
    ],

    // List configuration
    'list'     => [
        'sorting'           => [
            'mode'        => 1,
            'fields'      => ['title'],
            'flag'        => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label'             => [
            'fields' => ['title'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'workflow' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_example']['workflowBT'],
                'href'            => '',
                'icon'            => 'bundles/netzmachtcontaoworkflow/img/workflow.png',
                'button_callback' => [OperationListener::class, 'workflowOperationButton'],
            ],
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_example']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'copy'   => [
                'label'           => &$GLOBALS['TL_LANG']['tl_example']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.svg',
            ],
            'delete' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_example']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.svg',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_example']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,workflow,published',
    ],

    // Fields
    'fields'   => [
        'id'        => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_example']['title'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'published' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_example']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => false, 'tl_class' => 'm12 w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'workflow' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_example']['workflow'],
            'inputType'        => 'select',
            'exclude'          => true,
            'filter'           => true,
            'default'          => '',
            'eval'             => [
                'includeBlankOption' => true,
                'chosen'             => true,
                'submitOnChange'     => true,
                'tl_class'           => 'w50',
            ],
            'options_callback' => [OptionsListener::class, 'workflowOptions'],
            'sql'              => 'varchar(64) NOT NULL default \'\'',
        ],
        'workflowStep' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_example']['workflowStep'],
            'inputType'        => 'select',
            'exclude'          => true,
            'filter'           => true,
            'default'          => '',
            'eval'             => [
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
                'disabled'           => true,
            ],
            'options_callback' => [OptionsListener::class, 'stepOptions'],
            'sql'              => 'varchar(64) NOT NULL default \'\'',
        ]
    ]
];
