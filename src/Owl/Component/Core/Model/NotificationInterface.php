<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\File\Model\FileableInterface;
use Owl\Component\Notification\Model\NotificationInterface as BaseNotificationInterface;
use Owl\Component\User\Model\UserAwareInterface;

interface NotificationInterface extends
    BaseNotificationInterface,
    UserAwareInterface,
    FileableInterface
{
    public const GROUP_ASSIGNED_ALL = 'ALL';

    public function getAssignedGroup(): string;

    public function setAssignedGroup(string $assignedGroup): void;
}
