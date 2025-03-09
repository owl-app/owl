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

namespace Owl\Component\Core\Model;

use Owl\Component\File\Model\UploaderInterface;
use Owl\Component\Status\Model\OwnerInterface as StatusOwnerInterface;
use Owl\Component\User\Model\PermissionUserInterface;
use Owl\Component\User\Model\UserInterface as BaseUserInterface;

interface AdminUserInterface extends
    BaseUserInterface,
    PermissionUserInterface,
    StatusOwnerInterface,
    RoleAwareInterface,
    UploaderInterface
{
    public function getName(): ?string;

    public function getDisplayName(): string;

    public function setDisplayName(string $displayName): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getNote(): ?string;

    public function setNote(?string $note): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getPhone(): ?string;

    public function setPhone(?string $phone): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $code): void;

    public function getRegistration(): ?AdminUserRegistrationDataInterface;

    public function setRegistration(?AdminUserRegistrationDataInterface $registration): void;

    public function hasRegistration(): bool;
}
