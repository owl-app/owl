<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\DependencyInjection;

use Owl\Bundle\FileBundle\EventListener\FileUploadListener;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class OwlFileExtension extends AbstractResourceExtension
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
     * @param array<string, array<string, array<string, mixed>>> $resources
     * @return array<string, array<string, mixed>>
     */
    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('owl.file.subjects', $resources);

        $this->createFilesListeners(array_keys($resources), $container);

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

    /**
     * @param array<string> $fileSubjects
     */
    private function createFilesListeners(array $fileSubjects, ContainerBuilder $container): void
    {
        foreach ($fileSubjects as $fileSubject) {
            $fileChangeListener = new Definition(FileUploadListener::class, [
                new Reference('owl.file_uploader'),
            ]);

            $fileChangeListener
                ->setPublic(true)
                ->addTag('kernel.event_listener', [
                    'event' => 'owl.' . $fileSubject . '_file.pre_create',
                    'method' => 'uploadFile',
                    'lazy' => true,
                ])
                ->addTag('kernel.event_listener', [
                    'event' => 'owl.' . $fileSubject . '_file.pre_update',
                    'method' => 'uploadFile',
                    'lazy' => true,
                ])
            ;

            $container->setDefinition(sprintf('owl.listener.%s_upload_file', $fileSubject), $fileChangeListener);
        }
    }
}