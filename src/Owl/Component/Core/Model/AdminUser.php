<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\User\Model\User;

class AdminUser extends User implements AdminUserInterface
{
    /** @var string */
    protected $displayName;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $note;

    /** @var string */
    protected $localeCode;

    /** @var array */
    protected $permissions = [];

    /** @var RoleInterface */
    protected $role;

    /** @var Collection<array-key, NotificationAcceptedInterface> */
    protected $acceptedNotifications;

    /** @var AdminUserRegistrationDataInterface|null */
    protected $registration;

    /** @var mixed[] */
    protected $preferences = [];

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, NotificationAcceptedInterface> $this->acceptedNotifications */
        $this->acceptedNotifications = new ArrayCollection();

        $this->localeCode = 'en';
    }

    public function getName(): ?string
    {
        return $this->displayName;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $code): void
    {
        $this->localeCode = $code;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getRole(): ?RoleInterface
    {
        return $this->role;
    }

    public function setRole(?RoleInterface $role): void
    {
        $this->role = $role;
    }

    /**
     * @return Collection<array-key, NotificationAcceptedInterface>
     */
    public function getAcceptedNotifications(): Collection
    {
        return $this->acceptedNotifications;
    }

    public function addAcceptedNotification(NotificationAcceptedInterface $notification): void
    {
        if (!$this->hasAcceptedNotification($notification)) {
            $this->acceptedNotifications->add($notification);
        }
    }

    public function removeAcceptedNotification(NotificationAcceptedInterface $notification): void
    {
        if ($this->hasAcceptedNotification($notification)) {
            $this->acceptedNotifications->removeElement($notification);
        }
    }

    public function hasAcceptedNotification(NotificationAcceptedInterface $notification): bool
    {
        return $this->acceptedNotifications->contains($notification);
    }

    public function getRegistration(): ?AdminUserRegistrationDataInterface
    {
        return $this->registration;
    }

    public function setRegistration(?AdminUserRegistrationDataInterface $registration): void
    {
        $this->registration = $registration;

        if ($registration instanceof AdminUserRegistrationDataInterface) {
            $registration->setUser($this);
        }
    }

    public function hasRegistration(): bool
    {
        return null !== $this->registration;
    }

    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    public function setPreferences(array $preferences): void
    {
        $this->preferences = $preferences;
    }
}
