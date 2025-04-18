<?php

declare(strict_types=1);

namespace Owl\Component\Contractor\Model;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableTrait;

class Contractor implements ContractorInterface, ResourceInterface
{
    use TimestampableTrait;

    /** @var int|string */
    protected $id;

    /** @var string|null */
    protected $companyName;

    /** @var string|null */
    protected $taxNumber;

    /** @var string|null */
    protected $street;

    /** @var string|null */
    protected $buildingNumber;

    /** @var string|null */
    protected $flatNumber;

    /** @var string|null */
    protected $city;

    /** @var string|null */
    protected $postCode;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $countryCode;

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(?string $buildingNumber): void
    {
        $this->buildingNumber = $buildingNumber;
    }

    public function getFlatNumber(): ?string
    {
        return $this->flatNumber;
    }

    public function setFlatNumber(?string $flatNumber): void
    {
        $this->flatNumber = $flatNumber;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
