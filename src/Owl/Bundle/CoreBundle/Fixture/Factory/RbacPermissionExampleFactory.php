<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Fixture\Factory;

use Owl\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class RbacPermissionExampleFactory implements ExampleFactoryInterface
{
    public function __construct(
        private FactoryInterface $rbacPermissionFactory,
    ) {
    }

    /**
     * @param array{
     *     name?: string,
     *     group?: string,
     *     description?: string
     * } $options
     */
    public function create(array $options = []): PermissionInterface
    {
        $options = array_merge([
            'name' => null,
            'group' => null,
            'description' => null,
        ], $options);

        /** @var PermissionInterface $rbacPermission */
        $rbacPermission = $this->rbacPermissionFactory->createNew();
        $rbacPermission->setName($options['name']);
        $rbacPermission->setGroupPermission($options['group']);
        $rbacPermission->setDescription($options['description']);

        return $rbacPermission;
    }
}