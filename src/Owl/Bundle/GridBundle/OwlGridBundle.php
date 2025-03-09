<?php

declare(strict_types=1);

namespace Owl\Bundle\GridBundle;

use Owl\Bundle\GridBundle\DependencyInjection\Compiler\RegisterElasticSearchFiltersPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OwlGridBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterElasticSearchFiltersPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
    }
}
