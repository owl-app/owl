<?php

declare(strict_types=1);

namespace Owl\Bundle\GridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('owl_grid');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->addTemplatesSection($rootNode);

        return $treeBuilder;
    }

    private function addTemplatesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('elasticsearch')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('filter')
                                    ->useAttributeAsKey('name')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('action')
                                    ->useAttributeAsKey('name')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('bulk_action')
                                    ->useAttributeAsKey('name')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
