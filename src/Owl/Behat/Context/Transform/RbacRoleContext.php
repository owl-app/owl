<?php

declare(strict_types=1);

namespace Owl\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class RbacRoleContext implements Context
{
    public function __construct(
        private RepositoryInterface $rbacRoleRepository,
    ) {
    }

    /**
     * @Transform /^role(?:|s) "([^"]+)"$/
     * @Transform /^"([^"]+)" role(?:|s)$/
     * @Transform /^with role "([^"]+)"$/
     * @Transform /^(?:a|an) "([^"]+)"$/
     * @Transform :role
     */
    public function getCanonicalName($roleName)
    {
        $roles = $this->rbacRoleRepository->findBy(['name' => $roleName]);

        Assert::eq(
            count($roles),
            1,
            sprintf('%d role has been found with name "%s".', count($roles), $roleName),
        );

        return $roles[0];
    }
}
