<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\DependencyInjection;

use Owl\Bridge\SyliusResource\Controller\BaseController;
use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType;
use Owl\Component\Invoice\Model\Buyer;
use Owl\Component\Invoice\Model\Invoice;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('owl_invoice');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)
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
                        ->arrayNode('invoice')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Invoice::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(InvoiceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(InvoiceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('buyer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Buyer::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(BaseController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                    ->end()
                    // ->arrayNode('shop_billing_data')
                    //     ->addDefaultsIfNotSet()
                    //     ->children()
                    //         ->variableNode('options')->end()
                    //         ->arrayNode('classes')
                    //             ->addDefaultsIfNotSet()
                    //             ->children()
                    //                 ->scalarNode('model')->defaultValue(InvoiceShopBillingData::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('interface')->defaultValue(InvoiceShopBillingDataInterface::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('factory')->defaultValue(InvoiceShopBillingDataFactory::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('repository')->cannotBeEmpty()->end()
                    //             ->end()
                    //         ->end()
                    //     ->end()
                    // ->end()
                    // ->arrayNode('line_item')
                    //     ->addDefaultsIfNotSet()
                    //     ->children()
                    //         ->variableNode('options')->end()
                    //         ->arrayNode('classes')
                    //             ->addDefaultsIfNotSet()
                    //             ->children()
                    //                 ->scalarNode('model')->defaultValue(LineItem::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('interface')->defaultValue(LineItemInterface::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('factory')->defaultValue(LineItemFactory::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('repository')->cannotBeEmpty()->end()
                    //             ->end()
                    //         ->end()
                    //     ->end()
                    // ->end()
                    // ->arrayNode('tax_item')
                    //     ->addDefaultsIfNotSet()
                    //     ->children()
                    //         ->variableNode('options')->end()
                    //         ->arrayNode('classes')
                    //             ->addDefaultsIfNotSet()
                    //             ->children()
                    //                 ->scalarNode('model')->defaultValue(TaxItem::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('interface')->defaultValue(TaxItemInterface::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('factory')->defaultValue(TaxItemFactory::class)->cannotBeEmpty()->end()
                    //                 ->scalarNode('repository')->cannotBeEmpty()->end()
                    //             ->end()
                    //         ->end()
                    //     ->end()
                    // ->end()
                    // ->arrayNode('invoice_sequence')
                    //     ->addDefaultsIfNotSet()
                    //     ->children()
                    //         ->variableNode('options')->end()
                    //         ->arrayNode('classes')
                    //             ->addDefaultsIfNotSet()
                    //             ->children()
                    //                 ->scalarNode('model')->defaultValue(InvoiceSequence::class)->cannotBeEmpty()->end()
                    //                     ->scalarNode('interface')->defaultValue(InvoiceSequenceInterface::class)->cannotBeEmpty()->end()
                    //                     ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                    //                     ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                    //                     ->scalarNode('repository')->cannotBeEmpty()->end()
                    //                 ->end()
                    //             ->end()
                    //         ->end()
                    //     ->end()
                    // ->end()
                ->end()
            ->end()
        ;
    }
}
