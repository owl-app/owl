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

namespace Owl\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Owl\Behat\Service\SharedStorageInterface;
use Owl\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use Yiisoft\Rbac\ManagerInterface;

final class AdminUserContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ExampleFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
        private ManagerInterface $rbacManager,
        private ObjectManager $objectManager,
        private \ArrayAccess $minkParameters,
    ) {
    }

    /**
     * @Given /^there is(?:| also) an user system "([^"]+)" (with role "[^"]+") and identified by "[^"]+"$/
     * @Given /^there is(?:| also) an user system "([^"]+)" (with role "[^"]+")$/
     */
    public function thereIsAnUserSystemWithRole(
        string $email,
        ?RoleInterface $role,
        string $password = 'test123',
    ) {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userFactory->create([
            'email' => $email,
            'password' => $password,
            'enabled' => true,
            'role' => $role->getCanonicalName(),
        ]);

        $adminUser->setRole($role);

        $this->userRepository->add($adminUser);

        $this->rbacManager->assign($role->getName(), $adminUser->getId());

        $this->sharedStorage->set('user', $adminUser);
    }

    /**
     * @Given /^there is(?:| also) an user system "([^"]+)"$/
     */
    public function thereIsAnUserSystemIdentifiedBy(
        string $email,
        $password = 'test123',
    ) {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userFactory->create([
            'email' => $email,
            'password' => $password,
            'enabled' => true,
            'role' => null,
        ]);

        $this->userRepository->add($adminUser);

        $this->sharedStorage->set('user', $adminUser);
    }

    /**
     * @Given /^(this user) is using ("[^"]+" locale)$/
     * @Given /^(I) am using ("[^"]+" locale) for my panel$/
     */
    public function thisUserIsUsingLocale(AdminUserInterface $adminUser, $localeCode)
    {
        $adminUser->setLocaleCode($localeCode);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }
}
