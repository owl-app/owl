<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_ui');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('event')
            ->children()
                ->append($this->getEventsDefinition())
        ;

        return $treeBuilder;
    }

    private function getEventsDefinition(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('events');
        /** @var ArrayNodeDefinition $eventsNode */
        $eventsNode = $builder->getRootNode();

        $eventsNode
            ->useAttributeAsKey('event_name')
            ->arrayPrototype()
                ->fixXmlConfig('block')
                ->children()
                    ->arrayNode('blocks')
                        ->defaultValue([])
                        ->useAttributeAsKey('block_name')
                        ->arrayPrototype()
                            ->canBeDisabled()
                            ->children()
                                ->booleanNode('enabled')->defaultNull()->end()
                                ->arrayNode('context')->addDefaultsIfNotSet()->ignoreExtraKeys(false)->end()
                                ->scalarNode('template')->defaultNull()->end()
                                ->integerNode('priority')->defaultNull()->end()
                            ->end()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static fn (?string $template): array => ['template' => $template])
                            ->end()
        ;

        return $eventsNode;
    }
}
