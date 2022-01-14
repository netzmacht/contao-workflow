<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection\Pass;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function sprintf;

final class ViewFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidConfigurationException If content_type attribute is missing.
     */
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('netzmacht.contao_workflow.view.factory')) {
            return;
        }

        $serviceIds = $container->findTaggedServiceIds('netzmacht.contao_workflow.view_factory');
        $definition = $container->getDefinition('netzmacht.contao_workflow.view.factory');
        $factories  = (array) $definition->getArgument(0);

        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (! isset($attributes['content_type'])) {
                    throw new InvalidConfigurationException(
                        sprintf(
                            'Service "%s" is tagged as workflow view factory but content_type attribute is missing',
                            $serviceId
                        )
                    );
                }

                $factories[$attributes['content_type']] = new Reference($serviceId);
            }
        }

        $definition->setArgument(0, $factories);
    }
}
