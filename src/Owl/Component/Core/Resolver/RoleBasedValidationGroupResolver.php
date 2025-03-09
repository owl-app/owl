<?php

declare(strict_types=1);

namespace Owl\Component\Core\Resolver;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Symfony\Component\Form\FormInterface;

final class RoleBasedValidationGroupResolver implements RoleBasedValidationGroupResolverInterface
{
    public const ROLE_VALIDATION = [
        RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME => 'owl.role.admin_system',
        RoleAwareInterface::ROLE_USER_NAME => 'owl.role.user',
    ];

    private AdminUserContextInterface $adminUserContext;

    public function __construct(AdminUserContextInterface $adminUserContext)
    {
        $this->adminUserContext = $adminUserContext;
    }

    /**
     * @return list{0: 'owl', 1?: 'owl.role.user'|'owl.role.admin_system'}
     */
    public function __invoke(FormInterface $form): array
    {
        $validationGroups = ['owl'];
        $roleName = $this->adminUserContext->getRoleCanonicalName();

        if (isset(self::ROLE_VALIDATION[$roleName])) {
            array_push($validationGroups, self::ROLE_VALIDATION[$roleName]);
        }

        return $validationGroups;
    }
}
