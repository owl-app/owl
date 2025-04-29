<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableTrait;

class Buyer implements BuyerInterface, ResourceInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected int $id;

    /** @var string|null */
    protected ?string $company;

    /** @var string|null */
    protected ?string $taxNumber;

    /** @var string|null */
    protected string $street;

    /** @var string|null */
    protected string $city;

    /** @var string|null */
    protected string $postcode;

    /** @var string|null */
    protected string $countryCode;

    public function getId(): int
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

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
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
