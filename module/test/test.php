<?php

use Netzmacht\Contao\Workflow\Factory;

define('TL_MODE', 'BE');
error_reporting('E_ALL');
ini_set('display_errors', '1');

require_once '/var/www/html/dev/workflow/system/initialize.php';

/** @var Factory $factory */
$factory = $GLOBALS['container']['workflow.factory'];

$manager  = $factory->createManager('default');
$workflow = $manager->getWorkflowByName('test');
$entity   = $factory->createEntity(\ContentModel::findByPk(1));
$handler  = $manager->handle($entity);

//var_dump($workflow->getStartTransition());
if ($handler->validate()) {
    $state = $handler->transit();
  //  var_dump($state);
}
else {
    var_dump($handler->getContext()->getErrorCollection());
}
