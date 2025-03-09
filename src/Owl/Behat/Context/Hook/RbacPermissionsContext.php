<?php

declare(strict_types=1);

namespace Owl\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Owl\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Owl\Component\Rbac\Provider\RoutesPermissionProviderInterface;

final class RbacPermissionsContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoutesPermissionProviderInterface $routesPermissionProvider,
        private ExampleFactoryInterface $permissionFactory,
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function createPermissions()
    {
        $routes = $this->routesPermissionProvider->getPermissions();

        foreach ($routes as $name => $route) {
            $permission = $this->permissionFactory->create(array_merge(['name' => $name], $route));

            $this->entityManager->persist($permission);
        }

        $this->entityManager->flush();
    }
}
