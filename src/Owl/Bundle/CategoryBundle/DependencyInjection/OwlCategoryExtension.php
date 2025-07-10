<?php

declare(strict_types=1);

namespace Owl\Bundle\CategoryBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class OwlCategoryExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('owl', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        $loader->load('services.xml');

        $loader->load(sprintf('integrations/%s.xml', $config['driver']));
    }

    /**
     * @param array<string, array<string, mixed>> $resources
     *
     * @return array<string, mixed>
     */
    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('owl.category.subjects', $resources);

        $resolvedResources = [];
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName . '_' . $resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }
}
