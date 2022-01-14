<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function array_keys;
use function array_merge;
use function dirname;

final class NetzmachtContaoWorkflowExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__) . '/Resources/config')
        );

        // Common config, services and listeners
        $loader->load('templates.yml');
        $loader->load('actions.yml');
        $loader->load('controllers.yml');
        $loader->load('listeners.yml');
        $loader->load('renderer.yml');
        $loader->load('integration.yml');
        $loader->load('transitions.yml');
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $providers     = $config['providers'];

        foreach (array_keys($config['default_type']) as $provider) {
            $providers[$provider] = [
                'workflow'        => true,
                'step'            => true,
                'step_permission' => $config['default_type'][$provider]['step_permission'],
                'assign_users'    => $config['default_type'][$provider]['assign_users'],
            ];
        }

        $container->setParameter('netzmacht.contao_workflow.dca_providers', $providers);
        $container->setParameter('netzmacht.contao_workflow.type.default', $config['default_type']);
        /** @psalm-suppress PossiblyInvalidArgument */
        $container->setParameter(
            'netzmacht.contao_workflow.transition_types',
            array_merge($config['transitions'], $container->getParameter('netzmacht.contao_workflow.transition_types'))
        );
    }
}
