<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Factory\Resource;

use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Model\ResourceInterface;

/**
 * @template T of ResourceInterface
 *
 * @extends FactoryInterface<T>
 */
interface ParentableFactoryInterface extends FactoryInterface
{
    public function getResourceParents(string $name): ResourceInterface;

    /**
     * @param array<string, ResourceInterface> $resources
     */
    public function setResourceParents(array $resources): void;
}
