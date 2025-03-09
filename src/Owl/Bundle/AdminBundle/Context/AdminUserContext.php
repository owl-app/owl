<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Context;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Rbac\RoleSettingInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

final class AdminUserContext implements AdminUserContextInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): ?AdminUserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        $user = $token->getUser();
        if ($user instanceof AdminUserInterface) {
            return $user;
        }

        return null;
    }

    public function getRoleCanonicalName(): ?string
    {
        $user = $this->getUser();

        return $user?->getRole()?->getCanonicalName();
    }

    public function getTheme(): ?string
    {
        $setting = $this->getRoleSetting();

        if ($setting) {
            return $setting->getTheme();
        }

        return null;
    }

    public function isAdminSystem(): bool
    {
        return $this->getRoleCanonicalName() === RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME;
    }

    public function isUser(): bool
    {
        return $this->getRoleCanonicalName() === RoleAwareInterface::ROLE_USER_NAME;
    }

    private function getRoleSetting(): ?RoleSettingInterface
    {
        $user = $this->getUser();

        return $user?->getRole()?->getSetting();
    }
}
