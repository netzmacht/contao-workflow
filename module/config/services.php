<?php

use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use Netzmacht\Workflow\Contao\Data\EntityManager;
use Netzmacht\Workflow\Contao\Data\StateRepository;
use Netzmacht\Workflow\Contao\Data\EntityFactory;
use Netzmacht\Workflow\Contao\Data\RepositoryFactory;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Handler\Listener\EventDispatchingListener;
use Netzmacht\Workflow\Transaction\EventDispatchingTransactionHandler;

/** @var \Pimple $container */
global $container;

$container['workflow.service-provider'] = $container->share(
    function($container) {
        return new \Netzmacht\Workflow\Contao\ServiceProvider($container);
    }
);

$container['workflow.factory'] = $container->share(
    function($container) {
        return new Factory($container['event-dispatcher']);
    }
);

$container['workflow.security.authenticate'] = function() {
    if (TL_MODE === 'BE') {
        return true;
    }

    return false;
};

$container['workflow.translator'] = $container->share(
    function($container) {
        $chain = new TranslatorChain();
        $chain->add(new LangArrayTranslator($container['event-dispatcher']));

        return $chain;
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
        // load user before database is accessed
        try {
            $container['user'];
        } catch (\Exception $e) {}

        $factory = new RepositoryFactory($container['event-dispatcher']);

        return $factory;
    }
);

$container['workflow.factory.entity'] = $container->share(
    function() {
        $factory = new EntityFactory();

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


$container['workflow.transition.expression-language'] = $container->share(
    function($container) {
        return new \Symfony\Component\ExpressionLanguage\ExpressionLanguage();
    }
);
