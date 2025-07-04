<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Buyer;

use Sylius\Resource\Model\TimestampableTrait;

class Buyer implements BuyerInterface
{
    use TimestampableTrait;

    protected mixed $id;

    protected ?string $company = null;

    protected ?string $taxNumber = null;

    protected ?string $street = null;

    protected ?string $city = null;

    protected ?string $postcode = null;

    protected ?string $countryCode = null;

    public function __construct()
    {
        $this->id = null;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
