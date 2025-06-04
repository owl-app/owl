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

namespace Owl\Component\Location\Provider;

use Owl\Component\Location\Model\ProvinceCodeAwareInterface;

interface ProvinceNamingProviderInterface
{
    public function getName(ProvinceCodeAwareInterface $address): string;

    public function getAbbreviation(ProvinceCodeAwareInterface $address): string;
}
