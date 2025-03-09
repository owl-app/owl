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

namespace Owl\Bundle\LocaleBundle;

use Owl\Bundle\LocaleBundle\DependencyInjection\Compiler\CompositeLocaleContextPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OwlLocaleBundle extends AbstractResourceBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeLocaleContextPass());
    }

    /**
     * @return list{'doctrine/orm', 'doctrine/mongodb-odm'}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        ];
    }

    /**
     * @return 'Owl\Component\Locale\Model'
     */
    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Locale\Model';
    }
}
