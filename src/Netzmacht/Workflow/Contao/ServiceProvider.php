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

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Factory;
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
     * @var Pimple
     */
    private $container;

    /**
     * @param $container
     */
    function __construct(Pimple $container)
    {
        $this->container = $container;
    }

    /**
     * Create the service provider.
     *
     * @param Pimple $container Dependency injection container.
     *
     * @return static
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function create(Pimple $container = null)
    {
        $container = $container ?: $GLOBALS['container'];

        return new static($container);
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
     * Get a service from the DI.
     *
     * @param string $service Service name.
     *
     * @return mixed
     */
    private function getService($service)
    {
        return $this->container[$service];
    }
}
