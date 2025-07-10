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

namespace Owl\Bundle\UserBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface as BaseUserProviderInterface;

/**
 * @extends BaseUserProviderInterface<UserInterface>
 */
interface UserProviderInterface extends BaseUserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface;
}
