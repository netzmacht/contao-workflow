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

namespace Netzmacht\Contao\Workflow\Bundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class WorkflowTypePass
 */
class WorkflowTypePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_workflow.type_provider')) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_workflow.type_provider');
        $serviceIds = $container->findTaggedServiceIds('netzmacht.contao_workflow.type');
        $types      = (array) $definition->getArgument(0);

        foreach ($serviceIds as $serviceId => $tags) {
            $types[] = new Reference($serviceId);
        }

        $definition->setArgument(0, $types);
    }
}
