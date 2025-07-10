<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\User\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait, ToggleableTrait;

    /** @var mixed */
    protected $id;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string|null
     */
    protected $password;

    /**
     * Password before encryption. Used for model validation. Must not be persisted.
     *
     * @var string|null
     */
    protected $plainPassword;

    /** @var \DateTimeInterface|null */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string|null
     */
    protected $emailVerificationToken;

    /**
     * Random string sent to the user email address in order to verify the password resetting request
     *
     * @var string|null
     */
    protected $passwordResetToken;

    /** @var \DateTimeInterface|null */
    protected $passwordRequestedAt;

    /** @var \DateTimeInterface|null */
    protected $verifiedAt;

    /** @var bool */
    protected $locked = false;

    /** @var \DateTimeInterface|null */
    protected $expiresAt;

    /** @var \DateTimeInterface|null */
    protected $credentialsExpireAt;

    /**
     * We need at least one role to be able to authenticate
     *
     * @var array<string>
     */
    protected $roles;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $hasherName;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        // Set here to overwrite default value from trait
        $this->enabled = false;

        $this->roles = [];
    }

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * For BC to remove with symfony 6
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $encodedPassword): void
    {
        $this->password = $encodedPassword;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $date): void
    {
        $this->expiresAt = $date;
    }

    public function getCredentialsExpireAt(): ?\DateTimeInterface
    {
        return $this->credentialsExpireAt;
    }

    public function setCredentialsExpireAt(?\DateTimeInterface $date): void
    {
        $this->credentialsExpireAt = $date;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $time): void
    {
        $this->lastLogin = $time;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $verificationToken): void
    {
        $this->emailVerificationToken = $verificationToken;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function isCredentialsNonExpired(): bool
    {
        return !$this->hasExpired($this->credentialsExpireAt);
    }

    public function isAccountNonExpired(): bool
    {
        return !$this->hasExpired($this->expiresAt);
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool
    {
        if (null === $this->passwordRequestedAt) {
            return false;
        }

        $threshold = new \DateTime();
        $threshold->sub($ttl);

        return $threshold <= $this->passwordRequestedAt;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $date): void
    {
        $this->passwordRequestedAt = $date;
    }

    public function isVerified(): bool
    {
        return null !== $this->verifiedAt;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPasswordHasherName(): ?string
    {
        return $this->hasherName;
    }

    public function setPasswordHasherName(?string $hasherName): void
    {
        $this->hasherName = $hasherName;
    }

    /**
     * The serialized data have to contain the fields used by the equals method and the username.
     */
    public function serialize(): string
    {
        return serialize([
            $this->password,
            $this->email,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->hasherName,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        [
            $this->password,
            $this->email,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->hasherName,
        ] = $data;
    }

    protected function hasExpired(?\DateTimeInterface $date): bool
    {
        return null !== $date && new \DateTime() >= $date;
    }
}
