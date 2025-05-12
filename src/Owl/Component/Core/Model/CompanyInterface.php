<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Company\Model\CompanyInterface as BaseCompanyInterface;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface CompanyInterface extends BaseCompanyInterface, CountryCodeAwareInterface, ProvinceCodeAwareInterface
{
    public function getZone(): ?ZoneInterface;

    public function setZone(?ZoneInterface $zone): void;

    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;
}
