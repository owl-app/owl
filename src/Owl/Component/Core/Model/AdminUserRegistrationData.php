<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\User\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class AdminUserRegistrationData implements AdminUserRegistrationDataInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var BaseUserInterface */
    protected $user;

    /** @var string */
    protected $company;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $email;

    /** @var string */
    protected $city;

    /** @var string */
    protected $street;

    /** @var string */
    protected $postCode;

    /** @var string */
    protected $nip;

    /** @var string */
    protected $status = AdminUserRegistrationDataInterface::STATUS_NEW;

    /** @var \DateTimeInterface|null */
    protected $changeStatusAt;

    /** @var string */
    protected $reasonRejection;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?BaseUserInterface
    {
        return $this->user;
    }

    public function setUser(?BaseUserInterface $user): void
    {
        $this->user = $user;
    }

    public function hasUser(): bool
    {
        return null !== $this->user;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(?string $nip): void
    {
        $this->nip = $nip;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getChangeStatusAt(): ?\DateTimeInterface
    {
        return $this->changeStatusAt;
    }

    public function setChangeStatusAt(?\DateTimeInterface $changeStatusAt): void
    {
        $this->changeStatusAt = $changeStatusAt;
    }

    public function getReasonRejection(): ?string
    {
        return $this->reasonRejection;
    }

    public function setReasonRejection(?string $reasonRejection): void
    {
        $this->reasonRejection = $reasonRejection;
    }
}
