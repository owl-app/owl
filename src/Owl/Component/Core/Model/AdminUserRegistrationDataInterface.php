<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\User\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface AdminUserRegistrationDataInterface extends ResourceInterface
{
    public const STATUS_NEW = 'new';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public function getCompany(): ?string;

    public function setCompany(?string $company): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getPhone(): ?string;

    public function setPhone(?string $phone): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getCity(): ?string;

    public function setCity(?string $city): void;

    public function getStreet(): ?string;

    public function setStreet(?string $street): void;

    public function getPostCode(): ?string;

    public function setPostCode(?string $postCode): void;

    public function getNip(): ?string;

    public function setNip(?string $nip): void;

    public function getUser(): ?BaseUserInterface;

    public function setUser(?BaseUserInterface $user): void;

    public function hasUser(): bool;

    public function getStatus(): ?string;

    public function setStatus(?string $status): void;

    public function getChangeStatusAt(): ?\DateTimeInterface;

    public function setChangeStatusAt(?\DateTimeInterface $changeStatusAt): void;

    public function getReasonRejection(): ?string;

    public function setReasonRejection(?string $reasonRejection): void;
}
