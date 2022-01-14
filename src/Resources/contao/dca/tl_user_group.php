<?php

declare(strict_types=1);

use ContaoCommunityAlliance\MetaPalettes\MetaPalettes;

foreach (array_keys($GLOBALS['TL_DCA']['tl_user_group']['palettes']) as $palette) {
    if ($palette === '__selector__') {
        continue;
    }

    MetaPalettes::appendBefore('tl_user_group', $palette, 'account', ['workflow' => ['workflow']]);
}

$GLOBALS['TL_DCA']['tl_user_group']['fields']['workflow'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_user_group']['workflow'],
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
