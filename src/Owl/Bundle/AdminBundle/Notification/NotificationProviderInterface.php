<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Notification;

use Owl\Component\Core\Model\NotificationInterface;

interface NotificationProviderInterface
{
    /**
     * @return array<array-key, NotificationInterface>
     */
    public function getNotifications(): array;
}
