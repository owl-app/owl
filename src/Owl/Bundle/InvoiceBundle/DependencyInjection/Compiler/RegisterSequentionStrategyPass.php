<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterSequentionStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('owl.registry.invoice_sequence_increment_strategy')) {
            return;
        }

        $invoiceSequenceIncrementStrategyRegistry = $container->getDefinition('owl.registry.invoice_sequence_increment_strategy');
        $invoiceSequenceIncrementStrategyToLabelMap = [];

        foreach ($container->findTaggedServiceIds('owl.invoice.sequence_increment_strategy') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'])) {
                    throw new \InvalidArgumentException('Tagged invoice sequence increment strategy `' . $id . '` needs to have `type`, `label` attributes.');
                }

                $invoiceSequenceIncrementStrategyToLabelMap[$attribute['type']] = $attribute['label'];
                $invoiceSequenceIncrementStrategyRegistry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
            }
        }

        $container->setParameter('owl.invoice.sequence_increment_strategies', $invoiceSequenceIncrementStrategyToLabelMap);
    }
}
