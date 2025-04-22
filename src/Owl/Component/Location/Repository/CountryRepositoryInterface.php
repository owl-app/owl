<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\Location\Repository;

use Owl\Component\Location\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of CountryInterface
 *
 * @extends RepositoryInterface<T>
 */
interface CountryRepositoryInterface extends RepositoryInterface
{
}
