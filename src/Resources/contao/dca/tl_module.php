<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['workflow_transition'] = [
    'title'     => ['name', 'type', 'headline'],
    'config'    => ['workflow_providers', 'workflow_detach'],
    'redirect'  => ['jumpTo'],
    'template'  => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID', 'space'],
    'invisible' => [':hide', 'invisible', 'start', 'start'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['workflow_providers'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['workflow_providers'],
    'inputType'        => 'checkbox',
    'options_callback' => ['netzmacht.contao_workflow.listeners.dca.module', 'providerOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['MOD'],
    'eval'             => [
        'tl_class' => 'clr w50',
        'multiple' => true,
    ],
    'sql'              => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['workflow_detach'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['workflow_detach'],
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class'       => 'clr w50',
        'submitOnChange' => true,
    ],
    'sql'       => "char(1) NOT NULL default ''",
];
