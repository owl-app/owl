<?php

declare(strict_types=1);

namespace Owl\Bundle\GridBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Sylius\Component\Grid\Filtering\FormTypeAwareFilterInterface;
use Sylius\Component\Grid\Filtering\TypeAwareFilterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterElasticSearchFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('owl.registry.elasticsearch_grid_filter') || !$container->hasDefinition('owl.form_registry.elasticsearch_grid_filter')) {
            return;
        }

        $registry = $container->getDefinition('owl.registry.elasticsearch_grid_filter');
        $formTypeRegistry = $container->getDefinition('owl.form_registry.elasticsearch_grid_filter');

        foreach ($container->findTaggedServiceIds('owl.elasticsearch_grid_filter') as $id => $attributes) {
            $type = null;
            $formType = null;

            if (is_a($id, TypeAwareFilterInterface::class, true)) {
                $type = $id::getType();
            }

            if (is_a($id, FormTypeAwareFilterInterface::class, true)) {
                $formType = $id::getFormType();
            }

            foreach ($attributes as $attribute) {
                if (null === $type && null === ($attribute['type'] ?? null)) {
                    throw new InvalidArgumentException(sprintf('Tagged elasticsearch grid filters needs to have "type" attributes or implements "%s".', TypeAwareFilterInterface::class));
                }

                if (null === $formType && null === ($attribute['form_type'] ?? null)) {
                    throw new InvalidArgumentException(sprintf('Tagged grid filters needs to have "form_type" attributes or implements %s.', FormTypeAwareFilterInterface::class));
                }

                $registry->addMethodCall('register', [$type ?? $attribute['type'], new Reference($id)]);
                $formTypeRegistry->addMethodCall('add', [$type ?? $attribute['type'], 'default', $formType ?? $attribute['form_type']]);
            }
        }
    }
}
