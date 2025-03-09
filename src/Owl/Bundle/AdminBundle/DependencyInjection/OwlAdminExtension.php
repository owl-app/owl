<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class OwlAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('sylius.admin.notification.enabled', $config['notifications']['enabled']);
        $container->setParameter('sylius.admin.notification.frequency', $config['notifications']['frequency']);

        $loader->load('services.xml');
    }
}
