<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Owl\Component\Notification\Model\NotificationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of NotificationAcceptedInterface
 *
 * @implements NotificationAcceptedFactoryInterface<T>
 */
final class NotificationAcceptedFactory implements NotificationAcceptedFactoryInterface
{
    public function __construct(private FactoryInterface $defaultFactory)
    {
    }

    public function createNew(): NotificationAcceptedInterface
    {
        return $this->defaultFactory->createNew();
    }

    public function createAction(NotificationInterface $notification, AdminUserInterface $user): NotificationAcceptedInterface
    {
        /** @var NotificationAcceptedInterface $accepted */
        $accepted = $this->defaultFactory->createNew();
        $accepted->setNotification($notification);
        $accepted->setUser($user);

        return $accepted;
    }
}
