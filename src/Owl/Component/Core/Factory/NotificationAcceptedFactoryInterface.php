<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface NotificationAcceptedFactoryInterface extends FactoryInterface
{
    public function createAction(NotificationInterface $notification, AdminUserInterface $user): NotificationAcceptedInterface;
}
