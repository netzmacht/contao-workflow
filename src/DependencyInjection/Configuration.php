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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('netzmacht_contao_workflow');

        $rootNode
            ->children()
                ->arrayNode('default_type')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default_workflow')
                                ->info('Define the default workflow name.')
                            ->end()
                            ->arrayNode('palettes')
                                ->info('Define the dca palettes where the workflow fields should be added.')
                                ->defaultValue([])
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('integrations')
                                ->children()
                                    ->booleanNode('submit_buttons')
                                        ->info('Show transitions as submit buttons in the edit mask')
                                    ->end()
                                    ->booleanNode('operation')
                                        ->defaultValue(true)
                                        ->info('Show transitions as submit buttons in the edit mask')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
