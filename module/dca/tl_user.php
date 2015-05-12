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


\MetaPalettes::appendBefore('tl_user', 'custom', 'account', array('workflow' => array('workflow')));
\MetaPalettes::appendBefore('tl_user', 'extend', 'account', array('workflow' => array('workflow')));
\MetaPalettes::appendBefore('tl_user', 'admin', 'account', array('workflow' => array('workflow')));


$GLOBALS['TL_DCA']['tl_user']['fields']['workflow'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['workflow'],
    'inputType' => 'checkbox',
    'options_callback'   => array('Netzmacht\Workflow\Contao\Backend\Permission', 'getAllPermissions'),
    'save_callback' => array(
        new \Netzmacht\Workflow\Contao\Backend\Dca\SavePermissionsCallback('tl_user')
    ),
    'eval'      => array(
        'tl_class' => 'clr',
        'multiple' => true,
    ),
    'sql' => 'mediumblob NULL'
);
