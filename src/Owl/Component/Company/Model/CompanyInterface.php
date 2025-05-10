<?php
declare(strict_types=1);

namespace Owl\Component\Company\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface CompanyInterface extends TimestampableInterface, ResourceInterface
{
    public function getName():? string;

    public function setName(string $name): void;

    public function getTaxNumber():? string;

    public function setTaxNumber(string $taxNumber): void;

    public function getCity():? string;

    public function setCity(string $city): void;

    public function getStreet():? string;

    public function setStreet(string $street): void;

    public function getBuildingNumber(): ?string;

    public function setBuildingNumber(?string $buildingNumber): void;

    public function getFlatNumber(): ?string;

    public function setFlatNumber(?string $flatNumber): void;


    public function getPostCode():? string;

    public function setPostCode(string $postCode): void;
    
    public function getPhone():? string;

    public function setPhone(string $phone): void;

    public function getEmail():? string;

    public function setEmail(string $email): void;

    public function getContactPerson(): ?string;

    public function setContactPerson(?string $contactPerson): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;
}
