<?php

declare(strict_types=1);

namespace Owl\Component\Location\Repository;

use Owl\Component\Location\Model\ZoneMemberInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ZoneMemberInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ZoneMemberRepositoryInterface extends RepositoryInterface
{
}
