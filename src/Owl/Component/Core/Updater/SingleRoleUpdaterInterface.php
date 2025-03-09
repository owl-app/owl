<?php

declare(strict_types=1);

namespace Owl\Component\Core\Updater;

use Owl\Component\Core\Model\AdminUserInterface;

interface SingleRoleUpdaterInterface
{
    public function assign(AdminUserInterface $user): void;
}
