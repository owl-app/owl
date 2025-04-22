<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class OwlLocationBundle extends AbstractResourceBundle
{
    /** @return string[] */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Location\Model';
    }
}
