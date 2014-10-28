<?php

use Netzmacht\Contao\Workflow\Factory;

define('TL_MODE', 'BE');
error_reporting('E_ALL');
ini_set('display_errors', '1');

require_once '/var/www/html/dev/workflow/system/initialize.php';

/** @var Factory $factory */
$factory = $GLOBALS['container']['workflow.factory'];

$manager = $factory->createManager('default');

$content = \ContentModel::findAll(array('limit' => 1, 'return' => 'Model'));
$entity  = $factory->createEntity($content);

var_dump($entity);
