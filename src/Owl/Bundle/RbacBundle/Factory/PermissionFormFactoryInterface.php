<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Factory;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

interface PermissionFormFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function createByRoutes(RequestConfiguration $requestConfiguration): array;

    /**
     * @param array<string, mixed>|array<int, string> $assignedPermissions
     * @param array<string, mixed>|array<int, string> $disabledPermissions
     * @return array<string, mixed>
     */
    public function createByExists(RequestConfiguration $requestConfiguration, array $assignedPermissions, array $disabledPermissions = [], bool $withRoles = false): array;
}