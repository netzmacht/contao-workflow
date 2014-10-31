<?php

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use Netzmacht\Contao\Workflow\Entity\EntityManager;
use Netzmacht\Contao\Workflow\Factory;
use Netzmacht\Contao\Workflow\Transaction\EventBasedTransactionHandler;

/** @var \Pimple $container */
global $container;

$container['workflow.factory'] = $container->share(
    function($container) {
        return new Factory($container['event-dispatcher']);
    }
);

$container['workflow.transition-handler-factory'] = $container->share(
    function($container) {
        $factory = new \Netzmacht\Contao\Workflow\TransitionHandler\TransitionHandlerFactory(
            $container['workflow.entity-manager'],
            $container['workflow.transaction-handler'],
            new InputProvider(),
            $container['event-dispatcher']
        );

        $factory->useEventDispatching(true);

        return $factory;
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

$container['workflow.entity-manager'] = $container->share(
    function() {
        return new EntityManager();
    }
);

$container['workflow.state-repository'] = $container->share(
    function($container) {
        return $container['workflow.entity-manager']->getStateRepository();
    }
);
