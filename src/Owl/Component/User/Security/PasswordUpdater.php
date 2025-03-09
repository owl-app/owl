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

namespace Owl\Component\User\Security;

use Owl\Component\User\Model\CredentialsHolderInterface;

final class PasswordUpdater implements PasswordUpdaterInterface
{
    /** @var UserPasswordHasherInterface */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->userPasswordHasher = $passwordHasher;
    }

    public function updatePassword(CredentialsHolderInterface $user): void
    {
        if (!in_array($user->getPlainPassword(), ['', null], true)) {
            $user->setPassword($this->userPasswordHasher->hash($user));
            $user->eraseCredentials();
        }
    }
}
