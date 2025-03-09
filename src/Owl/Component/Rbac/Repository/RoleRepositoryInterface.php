<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Repository;

use Owl\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of RoleInterface
 *
 * @extends RepositoryInterface<T>
 */
interface RoleRepositoryInterface extends RepositoryInterface
{
}
