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

namespace Owl\Bundle\ApiBundle\ApiPlatform;

interface ApiResourceConfigurationMergerInterface
{
    /**
     * @param array[] $configs
     *
     * @return array[]
     */
    public function mergeConfigs(array ...$configs): array;
}