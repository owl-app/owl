<?php

declare(strict_types=1);

namespace Owl\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('owl_api');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
            ->end()
            ->children()
                ->variableNode('product_image_prefix')
                    ->defaultValue('media/image')
                ->end()
            ->end()
            ->children()
                ->arrayNode('filter_eager_loading_extension')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('restricted_resources')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('operations')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->canBeDisabled()
                                            ->children()
                                                ->booleanNode('enabled')->defaultTrue()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
