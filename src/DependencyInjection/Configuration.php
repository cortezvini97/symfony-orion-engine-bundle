<?php


namespace Orion\OrionEngine\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('orion_engine_symfony');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode("dir_views")->end()
                ->scalarNode("cache_dir")->end()
                ->scalarNode("directives_dir")->end()
                ->scalarNode("functions_dir")->end()
                ->scalarNode("debug")->end()
                ->scalarNode("deleteFile")->end()
                ->scalarNode("encore")->end()
            ->end();
        
        return $treeBuilder;
    }
}