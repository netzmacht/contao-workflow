<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Workflow\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ActionFactoriesPass
 */
class ActionFactoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_workflow.action_factory')) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_workflow.action_factory');
        $factories  = (array) $definition->getArgument(0);
        $serviceIds = $container->findTaggedServiceIds('netzmacht.contao_workflow.action');

        foreach (array_keys($serviceIds) as $serviceId) {
            $factories[] = new Reference($serviceId);
        }

        $definition->setArgument(0, $factories);
    }
}
