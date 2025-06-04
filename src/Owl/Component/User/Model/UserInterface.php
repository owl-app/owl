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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use SyliusLabs\Polyfill\Symfony\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;

interface UserInterface extends
    AdvancedUserInterface,
    CredentialsHolderInterface,
    ResourceInterface,
    \Serializable,
    TimestampableInterface,
    ToggleableInterface,
    PasswordHasherAwareInterface
{
    public function getEmail(): null|string;

    public function setEmail(?string $email): void;

    /**
     * Gets normalized username (should be used in search and sort queries).
     */
    public function setLocked(bool $locked): void;

    public function getEmailVerificationToken(): ?string;

    public function setEmailVerificationToken(?string $verificationToken): void;

    public function getPasswordResetToken(): ?string;

    public function setPasswordResetToken(?string $passwordResetToken): void;

    public function getPasswordRequestedAt(): ?\DateTimeInterface;

    public function setPasswordRequestedAt(?\DateTimeInterface $date): void;

    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool;

    public function isVerified(): bool;

    public function getVerifiedAt(): ?\DateTimeInterface;

    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void;

    public function getExpiresAt(): ?\DateTimeInterface;

    public function setExpiresAt(?\DateTimeInterface $date): void;

    public function getCredentialsExpireAt(): ?\DateTimeInterface;

    public function setCredentialsExpireAt(?\DateTimeInterface $date): void;

    public function getLastLogin(): ?\DateTimeInterface;

    public function setLastLogin(?\DateTimeInterface $time): void;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool;

    public function addRole(string $role): void;

    public function removeRole(string $role): void;

    public function setPasswordHasherName(?string $hasherName): void;
}
