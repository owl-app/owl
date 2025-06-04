<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\DependencyInjection;

use Owl\Bridge\SyliusResource\Controller\BaseController;
use Owl\Bundle\LocationBundle\Doctrine\ORM\CountryRepository;
use Owl\Bundle\LocationBundle\Doctrine\ORM\ProvinceRepository;
use Owl\Bundle\LocationBundle\Doctrine\ORM\ZoneMemberRepository;
use Owl\Bundle\LocationBundle\Doctrine\ORM\ZoneRepository;
use Owl\Bundle\LocationBundle\Form\Type\CountryType;
use Owl\Bundle\LocationBundle\Form\Type\ProvinceType;
use Owl\Bundle\LocationBundle\Form\Type\ZoneMemberType;
use Owl\Bundle\LocationBundle\Form\Type\ZoneType;
use Owl\Component\Location\Model\Country;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\Province;
use Owl\Component\Location\Model\ProvinceInterface;
use Owl\Component\Location\Model\Zone;
use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Model\ZoneMember;
use Owl\Component\Location\Model\ZoneMemberInterface;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('owl_location');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('provider')->defaultValue('owl.province_name_provider')->end()
                ->arrayNode('zone_member')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('country')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Country::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CountryInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CountryRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(CountryType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('province')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Province::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProvinceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProvinceRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ProvinceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('zone')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Zone::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ZoneInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ZoneRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ZoneType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('zone_member')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ZoneMember::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ZoneMemberInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ZoneMemberRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ZoneMemberType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
