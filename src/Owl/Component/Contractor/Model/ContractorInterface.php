<?php

declare(strict_types=1);

namespace Owl\Component\Contractor\Model;

use Sylius\Resource\Model\ResourceInterface;

interface ContractorInterface extends ResourceInterface
{
    public function getCompanyName(): ?string;

    public function setCompanyName(?string $companyName): void;

    public function getTaxNumber(): ?string;

    public function setTaxNumber(?string $taxNumber): void;

    public function getStreet(): ?string;

    public function setStreet(string $street): void;

    public function getBuildingNumber(): ?string;

    public function setBuildingNumber(?string $buildingNumber): void;

    public function getFlatNumber(): ?string;

    public function setFlatNumber(?string $flatNumber): void;

    public function getCity(): ?string;

    public function setCity(string $city): void;

    public function getPostCode(): ?string;

    public function setPostCode(string $postcode): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;
}
