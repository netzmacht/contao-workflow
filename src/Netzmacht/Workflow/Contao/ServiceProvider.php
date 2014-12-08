<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao;

use Netzmacht\Workflow\Contao\Data\EntityFactory;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Security\User;
use Pimple;

/**
 * Class ServiceProvider is a simple interface for getting workflow related services from the service container.
 *
 * @package Netzmacht\Workflow\Contao
 */
class ServiceProvider
{
    /**
     * The dependency container.
     *
     * @var Pimple
     */
    private $container;

    /**
     * Construct.
     *
     * @param Pimple $container The dependency container.
     */
    public function __construct(Pimple $container)
    {
        $this->container = $container;
    }


    /**
     * Get the service container.
     *
     * @return \Pimple
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get a service from the DI.
     *
     * @param string $service Service name.
     *
     * @return mixed
     */
    public function getService($service)
    {
        return $this->container[$service];
    }

    /**
     * Get the factory.
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->getService('workflow.factory');
    }

    /**
     * Get manager
     *
     * @param      $providerName
     * @param null $type
     *
     * @return Manager
     */
    public function getManager($providerName, $type = null)
    {
        /** @var ManagerRegistry $registry */
        $registry = $this->getService('workflow.manager-registry');

        if (!$registry->has($providerName, $type)) {
            $manager = $this->getFactory()->createManager($providerName, $type);
            $registry->set($providerName, $type, $manager);

            return $manager;
        }

        return $registry->get($providerName, $type);
    }

    /**
     * Get the workflow security user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->getService('workflow.security.user');
    }

    /**
     * Get the entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getService('workflow.entity-manager');
    }

    /**
     * Get state repository.
     *
     * @return StateRepository
     */
    public function getStateRepository()
    {
        return $this->getService('workflow.state-repository');
    }

    /**
     * @return TransitionHandlerFactory
     */
    public function getTransitionHandlerFactory()
    {
        return $this->getService('workflow.factory.transition-handler');
    }

    /**
     * Get entity factory.
     *
     * @return EntityFactory
     */
    public function getEntityFactory()
    {
        return $this->getService('workflow.factory.entity');
    }
}
