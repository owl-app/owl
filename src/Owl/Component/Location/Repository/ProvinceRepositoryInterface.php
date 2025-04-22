<?php

declare(strict_types=1);

namespace Owl\Component\Location\Repository;

use Owl\Component\Location\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ProvinceInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProvinceRepositoryInterface extends RepositoryInterface
{
}
