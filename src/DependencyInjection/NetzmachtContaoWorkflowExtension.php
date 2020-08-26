<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function array_keys;

/**
 * Class NetzmachtContaoWorkflowExtension
 */
final class NetzmachtContaoWorkflowExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
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
            ];
        }

        $container->setParameter('netzmacht.contao_workflow.dca_providers', $providers);
        $container->setParameter('netzmacht.contao_workflow.type.default', $config['default_type']);
        $container->setParameter(
            'netzmacht.contao_workflow.transition_types',
            array_merge($config['transitions'], $container->getParameter('netzmacht.contao_workflow.transition_types'))
        );
    }
}
