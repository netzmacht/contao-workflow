<?php

\MetaPalettes::appendBefore('tl_user', 'custom', 'account', array('workflow' => array('workflow')));
\MetaPalettes::appendBefore('tl_user', 'extend', 'account', array('workflow' => array('workflow')));
\MetaPalettes::appendBefore('tl_user', 'admin', 'account', array('workflow' => array('workflow')));


$GLOBALS['TL_DCA']['tl_user']['fields']['workflow'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['workflow'],
    'inputType' => 'checkbox',
    'options_callback'   => array('Netzmacht\Workflow\Contao\Backend\Permission', 'getAllPermissions'),
    'eval'      => array(
        'tl_class' => 'clr',
        'multiple' => true,
    ),
    'sql' => 'mediumblob NULL'
);
