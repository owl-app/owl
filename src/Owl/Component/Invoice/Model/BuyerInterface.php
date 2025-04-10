<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

interface BuyerInterface
{
    public function getCompany(): ?string;

    public function setCompany(?string $company): void;

    public function getTaxNumber(): ?string;

    public function setTaxNumber(?string $taxNumber): void;

    public function getStreet(): string;

    public function setStreet(string $street): void;

    public function getCity(): string;

    public function setCity(string $city): void;

    public function getPostcode(): string;

    public function setPostcode(string $postcode): void;

    public function getCountryCode(): string;

    public function setCountryCode(string $countryCode): void;
}
