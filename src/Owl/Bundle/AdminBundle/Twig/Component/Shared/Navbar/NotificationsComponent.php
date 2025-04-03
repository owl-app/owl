<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Owl\Bundle\AdminBundle\Notification\NotificationProviderInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class NotificationsComponent
{
    public function __construct(
        protected readonly NotificationProviderInterface $notificationProvider,
        protected readonly bool $areNotificationsEnabled,
    ) {
    }

    /** @return array<array-key, mixed> */
    #[ExposeInTemplate(name: 'notifications')]
    public function getNotifications(): array
    {
        if (!$this->areNotificationsEnabled) {
            return [];
        }

        return $this->notificationProvider->getNotifications();
    }
}
