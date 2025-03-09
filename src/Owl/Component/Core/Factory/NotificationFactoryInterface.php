<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of NotificationInterface
 *
 * @extends FactoryInterface<T>
 */
interface NotificationFactoryInterface extends FactoryInterface
{
    public function createAction(string $status, AdminUserInterface $owner): NotificationInterface;
}
