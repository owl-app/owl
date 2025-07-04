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

namespace Owl\Component\User\Model;

/**
 * @template User of UserInterface
 */
interface UserAwareInterface
{
    /**
     * @return User|null
     */
    public function getUser();

    /**
     * @param User|null $user
     */
    public function setUser($user): void;
}
