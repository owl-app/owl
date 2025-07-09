<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Notification;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Repository\NotificationRepositoryInterface;

class NotificationProvider implements NotificationProviderInterface
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private AdminUserContextInterface $adminUserContext,
    ) {
    }

    public function getNotifications(): array
    {
        return $this->notificationRepository->findAllNotAccepted(
            $this->adminUserContext->getUser(),
            $this->adminUserContext->getRoleCanonicalName(),
        );
    }
}