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

namespace Netzmacht\Contao\Workflow;

/**
 * Class ServiceProviderTrait.
 *
 * Injecting the container or use the Container as a service locator is a bad thing. This is used because Contao
 * does not provide dependency injection.
 *
 * @package Netzmacht\Contao\Workflow
 */
trait ServiceContainerTrait
{
    use \Netzmacht\Contao\Toolkit\ServiceContainerTrait;

    /**
     * Get the service provider.
     *
     * @return ServiceProvider
     */
    protected function getServiceProvider()
    {
        return $this->getServiceContainer()->getService('workflow.service-provider');
    }
}
