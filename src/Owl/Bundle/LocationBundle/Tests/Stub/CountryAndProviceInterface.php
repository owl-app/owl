<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\LocationBundle\Stub;

use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;

interface CountryAndProviceInterface extends CountryCodeAwareInterface, ProvinceCodeAwareInterface
{
}
