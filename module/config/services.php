<?php

use Netzmacht\Contao\Workflow\Factory;
use Netzmacht\Contao\Workflow\Transaction\EventBasedTransactionHandler;

/** @var \Pimple $container */
global $container;

$container['workflow.factory'] = $container->share(
    function($container) {
        return new Factory($container['event-dispatcher']);
    }
);

$container['workflow.transaction-handler'] = $container->share(
    function($container) {
        return new EventBasedTransactionHandler($container['event-dispatcher']);
    }
);

$container['workflow.repository-factory'] = $container->share(
    function() {
        return new Factory\RepositoryFactory();
    }
);

$container['workflow.state-repository'] = $container->share(
    function($container) {
        return $container['workflow.repository-factory']->createStateRepository();
    }
);
