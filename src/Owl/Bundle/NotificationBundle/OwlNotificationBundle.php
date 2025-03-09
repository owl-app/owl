<?php

declare(strict_types=1);

namespace Owl\Bundle\NotificationBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class OwlNotificationBundle extends AbstractResourceBundle
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
     * @return 'Owl\Component\Notification\Model'
     */
    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Notification\Model';
    }
}
