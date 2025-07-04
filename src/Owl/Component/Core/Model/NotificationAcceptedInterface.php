<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Notification\Model\NotificationAwareInterface;
use Owl\Component\User\Model\UserAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @extends UserAwareInterface<AdminUserInterface>
 * @extends NotificationAwareInterface<NotificationInterface>
 */
interface NotificationAcceptedInterface extends
    ResourceInterface,
    UserAwareInterface,
    NotificationAwareInterface
{
}
