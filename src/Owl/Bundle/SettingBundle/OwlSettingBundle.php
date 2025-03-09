<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class OwlSettingBundle extends AbstractResourceBundle
{
    /**
     * @return list{'doctrine/orm'}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * @return 'Owl\Component\Setting\Model'
     */
    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Setting\Model';
    }
}
