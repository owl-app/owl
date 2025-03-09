<?php

declare(strict_types=1);

namespace Owl\Component\Status\Factory;

use Owl\Component\Status\Model\OwnerInterface;
use Owl\Component\Status\Model\StatusInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of StatusInterface
 *
 * @extends FactoryInterface<T>
 */
interface StatusFactoryInterface extends FactoryInterface
{
    public function createForSubjectWithOwner(string $parentName, ?OwnerInterface $owner): StatusInterface;
}
