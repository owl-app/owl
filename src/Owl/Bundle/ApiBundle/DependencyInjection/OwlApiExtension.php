<?php

declare(strict_types=1);

namespace Owl\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class OwlApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        // $container->setParameter('sylius_api.enabled', $config['enabled']);
        // $container->setParameter('sylius_api.product_image_prefix', $config['product_image_prefix']);
        // $container->setParameter(
        //     'sylius_api.filter_eager_loading_extension.restricted_resources',
        //     $config['filter_eager_loading_extension']['restricted_resources']
        // );

        $loader->load('services.xml');

        if ($container->hasParameter('api_platform.enable_swagger_ui') && $container->getParameter('api_platform.enable_swagger_ui')) {
            $loader->load('integrations/swagger.xml');
        }
    }
}
