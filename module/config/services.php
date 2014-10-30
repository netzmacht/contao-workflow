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

$container['workflow.acl-manager'] = $container->share(
    function($container) {
        $user = \BackendUser::getInstance();

        return new \Netzmacht\Contao\Workflow\Acl\BackendAclManager($user);
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
