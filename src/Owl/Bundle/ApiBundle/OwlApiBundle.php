<?php

declare(strict_types=1);

namespace Owl\Bundle\ApiBundle;

use Owl\Bundle\ApiBundle\DependencyInjection\Compiler\CommandDataTransformerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OwlApiBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        // $container->addCompilerPass(new CommandDataTransformerPass());
    }
}
