<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\DependencyInjection\Compiler;

use Owl\Component\Status\Factory\StatusFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterStatusFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var string $subject */
        foreach ($container->getParameter('owl.status.subjects') as $subject => $configuration) {
            $factory = $container->findDefinition('owl.factory.' . $subject . '_status');

            $statusFactoryDefinition = new Definition(StatusFactory::class, [$factory]);
            $statusFactoryDefinition->setPublic(true);

            $container->setDefinition('owl.factory.' . $subject . '_status', $statusFactoryDefinition);
        }
    }
}
