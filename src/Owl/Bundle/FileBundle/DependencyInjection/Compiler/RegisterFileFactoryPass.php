<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\DependencyInjection\Compiler;

use Owl\Component\File\Factory\FileFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterFileFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getParameter('owl.file.subjects') as $subject => $configuration) {
            $factory = $container->findDefinition('owl.factory.' . $subject . '_file');

            $fileFactoryDefinition = new Definition(FileFactory::class, [$factory]);
            $fileFactoryDefinition->setPublic(true);

            $container->setDefinition('owl.factory.' . $subject . '_file', $fileFactoryDefinition);
        }
    }
}
