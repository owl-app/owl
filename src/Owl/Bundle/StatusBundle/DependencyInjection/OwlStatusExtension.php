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

namespace Owl\Bundle\StatusBundle\DependencyInjection;

use Owl\Bundle\StatusBundle\EventListener\UpdateSubjectStatusListener;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class OwlStatusExtension extends AbstractResourceExtension
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
     * @param array<string, array<string, array>> $resources
     * @return array<string, array>
     */
    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('owl.status.subjects', $resources);

        $this->createStatusesListeners(array_keys($resources), $container);

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

    private function createStatusesListeners(array $statusSubjects, ContainerBuilder $container): void
    {
        foreach ($statusSubjects as $statusSubject) {
            $updateSubjectStatusListener = new Definition(UpdateSubjectStatusListener::class, [
                new Reference(sprintf('owl.manager.%s_status', $statusSubject)),
            ]);

            $updateSubjectStatusListener
                ->setPublic(true)
                ->addTag('doctrine.event_listener', [
                    'event' => 'postPersist',
                    'lazy' => true,
                ])
            ;

            $container->setDefinition(sprintf('owl.listener.%s_update_subject_status', $statusSubject), $updateSubjectStatusListener);
        }
    }
}