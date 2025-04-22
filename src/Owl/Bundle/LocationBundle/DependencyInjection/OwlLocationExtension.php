<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class OwlLocationExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('owl', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('owl.location.zone_member.validation_groups', $config['zone_member']['validation_groups']);
    }
}
