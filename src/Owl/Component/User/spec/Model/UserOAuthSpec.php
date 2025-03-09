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

namespace spec\Owl\Component\User\Model;

use Owl\Component\User\Model\UserOAuthInterface;
use PhpSpec\ObjectBehavior;

final class UserOAuthSpec extends ObjectBehavior
{
    function it_implements_user_oauth_interface(): void
    {
        $this->shouldImplement(UserOAuthInterface::class);
    }
}
