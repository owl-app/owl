<?php

declare(strict_types=1);

namespace Owl\Component\Location\Factory;

use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @extends FactoryInterface<T>
 */
interface ZoneFactoryInterface extends FactoryInterface
{
    public function createTyped(string $type): ZoneInterface;

    public function createWithMembers(array $membersCodes): ZoneInterface;
}
