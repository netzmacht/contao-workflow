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


foreach (array_keys($GLOBALS['TL_DCA']['tl_user_group']['palettes']) as $palette) {
    if ($palette === '__selector__') {
        continue;
    }

    \MetaPalettes::appendBefore('tl_user_group', $palette, 'account', array('workflow' => array('workflow')));
}

$GLOBALS['TL_DCA']['tl_user_group']['fields']['workflow'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_user_group']['workflow'],
    'inputType' => 'checkbox',
    'options_callback'   => array('Netzmacht\Workflow\Contao\Backend\Permission', 'getAllPermissions'),
    'eval'      => array(
        'tl_class' => 'clr',
        'multiple' => true,
    ),
    'sql' => 'mediumblob NULL'
);
