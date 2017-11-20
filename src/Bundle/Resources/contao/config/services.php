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


use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Workflow\Entity\EntityManager;
use Netzmacht\Contao\Workflow\Data\StateRepository;
use Netzmacht\Contao\Workflow\Data\EntityFactory;
use Netzmacht\Contao\Workflow\Data\RepositoryFactory;
use Netzmacht\Contao\Workflow\ManagerRegistry;
use Netzmacht\Contao\Workflow\ServiceProvider;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeProvider;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Handler\Listener;
use Netzmacht\Workflow\Handler\Listener\EventDispatchingListener;
use Netzmacht\Workflow\Transaction\EventDispatchingTransactionHandler;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/** @var \Pimple $container */
global $container;

/**
 *
 */
$container['workflow.service-provider'] = $container->share(
    function($container) {
        return new ServiceProvider($container);
    }
);

/**
 * Create the workflow factory.
 *
 * @return Factory
 */
$container['workflow.factory'] = $container->share(
    function($container) {
        return new Factory($container['event-dispatcher']);
    }
);

/**
 * Workflow manager registry.
 *
 * @return ManagerRegistry
 */
$container['workflow.manager-registry'] = $container->share(
    function() {
        return new ManagerRegistry();
    }
);

/**
 * Create the translator being used in the workflow extension.
 *
 * @return TranslatorInterface
 */
$container['workflow.translator'] = $container->share(
    function($container) {
        $chain = new TranslatorChain();
        $chain->add(new LangArrayTranslator($container['event-dispatcher']));

        return $chain;
    }
);

/**
 * Get the database instance.
 *
 * @param $container
 *
 * @return \Database
 */
$container['worfklow.database.connection'] = function($container) {
    return $container['database.connection'];
};

/**
 * Create the workflow transition listener.
 *
 * @param $container
 *
 * @return Listener
 */
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

/**
 * Create a shared instance of the repository factory.
 *
 * @return RepositoryFactory
 */
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

/**
 * Create the entity factory.
 *
 * @return EntityFactory
 */
$container['workflow.factory.entity'] = function() {
    $factory = new EntityFactory();

    return $factory;
};

/**
 * Create a shared entity manager service.
 *
 * @return EntityManager
 */
$container['workflow.entity-manager'] = $container->share(
    function($container) {
        return new EntityManager(
            $container['worfklow.database.connection'],
            $container['workflow.factory.repository']
        );
    }
);

/**
 * Create an instance of the workflow state repository.
 *
 * @return StateRepository
 */
$container['workflow.state-repository'] = function() {
    return new StateRepository();
};


/**
 * Shared instance of the expression language being used for expession conditions.
 *
 * It's a shared service so that you can provide additional language functions for it.
 *
 * @return ExpressionLanguage
 */
$container['workflow.transition.expression-language'] = $container->share(
    function() {
        return new ExpressionLanguage();
    }
);


/**
 * The Contao user repository.
 *
 * @return \Netzmacht\Contao\Workflow\Repository\UserRepository
 */
$container['workflow.repository.user'] = $container->share(
    function ($container) {
        return new \Netzmacht\Contao\Workflow\Repository\UserRepository(
            $container['worfklow.database.connection'],
            \Model\Registry::getInstance()
        );
    }
);

/**
 * Workflow type provider.
 *
 * @return WorkflowTypeProvider
 */
$container['workflow.type-provider'] = $container->share(
    function () {
        $types = array_map(
            function($typeClass) {
                return new $typeClass;
            },
            (array) $GLOBALS['WORKFLOW_TYPES']
        );

        return new WorkflowTypeProvider($types);
    }
);
