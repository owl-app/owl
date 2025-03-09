<?php

declare(strict_types=1);

namespace Owl\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Persistence\ObjectManager;
use Owl\Behat\Service\SharedStorageInterface;
use Owl\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Model\Rbac\RoleSetting;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Role;

final class RbacRoleContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ExampleFactoryInterface $rbacRoleFactory,
        private RepositoryInterface $rbacRoleRepository,
        private ManagerInterface $rbacManager,
        private ObjectManager $objectManager,
        private \ArrayAccess $minkParameters,
    ) {
    }

    /**
     * @Given the user operates on role :name
     * @Given the system has role :name
     */
    public function userOperatesOnRole($name)
    {
        $role = $this->createRole($name);
        $this->sharedStorage->set('role', $role);
    }

    /**
     * @Given the system has role:
     */
    public function systemHasRole(TableNode $table)
    {
        $roleData = $table->getHash()[0];
        $createdRole = $this->createRoleWtihSettings($roleData['name'], $roleData['display_name'], $roleData['theme']);

        $this->sharedStorage->set('role', $createdRole);
    }

    /**
     * @Given the system has roles with permisssions:
     */
    public function systemHasRolesWithPermissions(TableNode $table)
    {
        foreach ($table as $row) {
            $role = $this->createRoleWtihSettings($row['name'], $row['display_name'], $row['theme']);

            if (isset($row['permissions']) && !empty($row['permissions'])) {
                $permissions = explode(',', $row['permissions']);
                $this->createRolePermissions($role, $permissions);
            }
        }
    }

    /**
     * @Given /^(this role) has a setting "([^"]+)" with theme "([^"]+)"$/
     */
    public function thisRoleHasSettingWithTheme(
        RoleInterface $role,
        string $displayName,
        string $theme,
    ): void {
        $this->createRoleSettings($role, $displayName, $theme);
    }

    /**
     * @Given /^(this role) has a permission "([^"]+)"$/
     */
    public function thisRoleHasAPermission(
        RoleInterface $role,
        string $permission,
    ): void {
        $this->createRolePermissions($role, [$permission]);
    }

    /**
     * @Given /^(this role) has a permissions:$/
     */
    public function thisRoleHasAPermissions(
        RoleInterface $role,
        TableNode $table,
    ): void {
        foreach ($table as $row) {
            $permissions[] = $row['route'];
        }
        $this->createRolePermissions($role, $permissions);
    }

    private function createRoleWtihSettings(string $name, string $displayName, string $theme): RoleInterface
    {
        $role = $this->createRole($name);

        $this->createRoleSettings($role, $displayName, $theme);

        return $role;
    }

    private function createRole(string $name): RoleInterface
    {
        $this->rbacManager->addRole(new Role($name));

        $roles = $this->rbacRoleRepository->findBy(['name' => $name]);
        Assert::eq(
            count($roles),
            1,
            sprintf('%d role has been found with name "%s".', count($roles), $name),
        );

        return $roles[0];
    }

    private function createRoleSettings(RoleInterface $role, string $displayName, string $theme): void
    {
        $setting = new RoleSetting();
        $setting->setCanonicalName($displayName);
        $setting->setTheme($theme);

        $role->setSetting($setting);

        $this->objectManager->flush();
    }

    private function createRolePermissions(RoleInterface $role, array $permissions): void
    {
        foreach ($permissions as $permissionName) {
            $this->rbacManager->addChild($role->getName(), trim($permissionName));
        }
    }
}
