<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('netzmacht_contao_workflow');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('providers')
                    ->defaultValue([])
                    ->info('Define a list of dca containers where workflow fields should be auto created')
                    ->arrayPrototype()
                            ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('workflow')
                                ->defaultValue(true)
                                ->info('If true workflow field is created')
                            ->end()
                            ->booleanNode('step')
                                ->defaultValue(false)
                                ->info('If true step field is created')
                            ->end()
                            ->booleanNode('step_permission')
                                ->defaultValue(false)
                                ->info('If true step permission field is created')
                            ->end()
                            ->arrayNode('assign_users')
                                ->defaultValue([])
                                ->info('List fields where users can be assigned')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                                ->booleanNode('submit_buttons')
                                ->info('Show transitions as submit buttons in the edit mask')
                            ->end()
                                ->scalarNode('operation')
                                ->defaultValue(true)
                                ->info('Show transitions as submit buttons in the edit mask')
                            ->end()
                            ->booleanNode('step_permission')
                                ->defaultValue(false)
                                ->info('If true step permission field is created')
                            ->end()
                            ->arrayNode('assign_users')
                                ->defaultValue([])
                                ->info('List fields where users can be assigned')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('transitions')
                    ->defaultValue([])
                    ->info('Define transition types')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->info('The transition type name')
                            ->end()
                            ->booleanNode('step')
                                ->defaultValue(true)
                                ->info('Defines if the transition type have a configured step')
                            ->end()
                            ->booleanNode('actions')
                                ->defaultValue(true)
                                ->info('Defines if the transition type may have configured actions')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
