<?php

declare(strict_types=1);

namespace Owl\Component\Core\Context;

use Owl\Component\Core\Model\AdminUserInterface;

interface AdminUserContextInterface
{
    public function getUser(): ?AdminUserInterface;

    public function getRoleCanonicalName(): ?string;

    public function getTheme(): ?string;

    public function isAdminSystem(): bool;

    public function isUser(): bool;
}
