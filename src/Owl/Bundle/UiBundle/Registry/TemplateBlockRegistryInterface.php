<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\Registry;

/**
 * @experimental
 */
interface TemplateBlockRegistryInterface
{
    /**
     * @return array<string, array<string, TemplateBlock>>
     */
    public function all(): array;

    /**
     * @param non-empty-list<string> $eventNames
     *
     * @return list<TemplateBlock>
     */
    public function findEnabledForEvents(array $eventNames): array;
}
