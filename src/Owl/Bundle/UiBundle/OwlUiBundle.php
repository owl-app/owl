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

namespace Owl\Bundle\UiBundle;

use Owl\Bundle\UiBundle\DependencyInjection\Compiler\LiveComponentTagPass;
use Owl\Bundle\UiBundle\DependencyInjection\Compiler\TwigComponentTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This bundle provides generic UI for Sylius bundles and platform.
 */
final class OwlUiBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LiveComponentTagPass(), priority: 500);
        $container->addCompilerPass(new TwigComponentTagPass(), priority: 500);
    }
}
