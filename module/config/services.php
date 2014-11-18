<?php

use Netzmacht\Workflow\Contao\Data\EntityManager;
use Netzmacht\Workflow\Contao\Data\StateRepository;
use Netzmacht\Workflow\Contao\Data\EntityFactory;
use Netzmacht\Workflow\Contao\Data\RepositoryFactory;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Handler\Listener\EventDispatchingListener;
use Netzmacht\Workflow\Transaction\EventDispatchingTransactionHandler;

/** @var \Pimple $container */
global $container;

$container['workflow.factory'] = $container->share(
    function($container) {
        return new Factory($container['event-dispatcher']);
    }
);

$container['workflow.security.user'] = $container->share(
    function($container) {
        /** @var Factory $factory */
        $factory = $container['workflow.factory'];

        return $factory->createUser();
    }
);

$container['worfklow.database.connection'] = $container->share(
    function() {
        return \Database::getInstance();
    }
);

$container['workflow.transition.listener'] = function($container) {
    return new EventDispatchingListener($container['event-dispatcher']);
};

$container['workflow.factory.transition-handler'] = $container->share(
    function($container) {
        $factory = new Factory\RepositoryBasedTransitionHandlerFactory(
            $container['workflow.entity-manager'],
            $container['workflow.transaction-handler']
        );

        $factory->setListener($container['workflow.transition.listener']);

        return $factory;
    }
);

$container['workflow.transaction-handler'] = $container->share(
    function($container) {
        return new EventDispatchingTransactionHandler($container['event-dispatcher']);
    }
);

$container['workflow.factory.repository'] = $container->share(
    function($container) {
        $factory = new RepositoryFactory($container['event-dispatcher']);
        $container['event-dispatcher']->addSubscriber($factory);

        return $factory;
    }
);

$container['workflow.factory.entity'] = $container->share(
    function($container) {
        $factory = new EntityFactory();
        $container['event-dispatcher']->addSubscriber($factory);

        return $factory;
    }
);

$container['workflow.entity-manager'] = $container->share(
    function($container) {
        return new EntityManager(
            $container['worfklow.database.connection'],
            $container['workflow.factory.repository']
        );
    }
);

$container['workflow.state-repository'] = function() {
    return new StateRepository();
};
