<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of NotificationInterface
 *
 * @implements NotificationFactoryInterface<T>
 */
final class NotificationFactory implements NotificationFactoryInterface
{
    private FactoryInterface $defaultFactory;

    public function __construct(FactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
    }

    public function createNew(): NotificationInterface
    {
        return $this->defaultFactory->createNew();
    }

    public function createAction(string $status, AdminUserInterface $owner): NotificationInterface
    {
        /** @var NotificationInterface $channel */
        $notification = $this->defaultFactory->createNew();
        $notification->setUser($owner);

        return $notification;
    }
}
