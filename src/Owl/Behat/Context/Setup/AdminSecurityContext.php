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
use Owl\Behat\Service\SecurityServiceInterface;
use Owl\Behat\Service\SharedStorageInterface;
use Owl\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class AdminSecurityContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private SecurityServiceInterface $securityService,
        private ExampleFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @Given /^I am logged in as "([^"]+)" user$/
     */
    public function iAmLoggedInAsAdministrator($email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('logged_user', $user);
    }

    /**
     * @Given I have been logged out from administration
     */
    public function iHaveBeenLoggedOutFromAdministration()
    {
        $this->securityService->logOut();

        $this->sharedStorage->set('logged_user', null);
    }
}
