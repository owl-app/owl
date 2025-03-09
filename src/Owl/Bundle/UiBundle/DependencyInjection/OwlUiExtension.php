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

use Laminas\Stdlib\SplPriorityQueue;
use Owl\Bundle\UiBundle\Registry\TemplateBlock;
use Owl\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class OwlUiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $container->setParameter('owl_ui.twig_ux.anonymous_component_template_prefixes', $config['twig_ux']['anonymous_component_template_prefixes'] ?? []);
        $container->setParameter('owl_ui.twig_ux.live_component_tags', $config['twig_ux']['live_component_tags'] ?? []);
        $container->setParameter('owl_ui.twig_ux.component_default_template', $config['twig_ux']['component_default_template']);
    }
}
