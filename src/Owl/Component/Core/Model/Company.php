<?php
declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Company\Model\Company as BaseCompany;
use Owl\Component\Location\Model\ZoneInterface;

class Company extends BaseCompany implements CompanyInterface
{
    /** @var string|null */
    protected $countryCode;

    /** @var string|null */
    protected $provinceCode;

    /** @var ZoneInterface|null */
    protected $zone;

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        if (null === $countryCode) {
            $this->provinceCode = null;
        }

        $this->countryCode = $countryCode;
    }

    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function setProvinceCode(?string $provinceCode): void
    {
        $this->provinceCode = $provinceCode;
    }

    public function getZone(): ?ZoneInterface
    {
        return $this->zone;
    }

    public function setZone(?ZoneInterface $zone): void
    {
        $this->zone = $zone;
    }
}
