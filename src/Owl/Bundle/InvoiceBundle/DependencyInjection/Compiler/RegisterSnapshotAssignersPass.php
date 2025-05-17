<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterSnapshotAssignersPass implements CompilerPassInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('owl.registry.invoice_snapshot_assigner')) {
            return;
        }

        $registry = $container->findDefinition('owl.registry.invoice_snapshot_assigner');

        foreach ($container->findTaggedServiceIds('owl.invoice.snapshot_assigner') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                $priority = (int) ($attribute['priority'] ?? 0);

                $registry->addMethodCall('register', [new Reference($id), $priority]);
            }
        }
    }
}
