<?php

declare(strict_types=1);

use ContaoCommunityAlliance\MetaPalettes\MetaPalettes;

MetaPalettes::appendBefore('tl_user', 'custom', 'account', ['workflow' => ['workflow']]);
MetaPalettes::appendBefore('tl_user', 'extend', 'account', ['workflow' => ['workflow']]);
MetaPalettes::appendBefore('tl_user', 'admin', 'account', ['workflow' => ['workflow']]);


$GLOBALS['TL_DCA']['tl_user']['fields']['workflow'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_user']['workflow'],
    'inputType'        => 'checkbox',
    'options_callback' => ['netzmacht.contao_workflow.listeners.dca.permissions', 'getAllPermissions'],
    'save_callback'    => [
        ['netzmacht.contao_workflow.listeners.dca.save_permission_callback', 'onSaveCallback'],
    ],
    'eval'             => [
        'tl_class' => 'clr',
        'multiple' => true,
    ],
    'sql'              => 'mediumblob NULL',
];
