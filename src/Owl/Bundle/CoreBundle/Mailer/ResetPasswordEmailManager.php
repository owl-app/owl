<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Mailer;

use Owl\Component\User\Model\UserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ResetPasswordEmailManager implements ResetPasswordEmailManagerInterface
{
    public function __construct(private SenderInterface $emailSender)
    {
    }

    public function sendAdminResetPasswordEmail(UserInterface $user, string $localCode): void
    {
        $this->emailSender->send(
            code: Emails::ADMIN_PASSWORD_RESET,
            recipients: [$user->getEmail()],
            data: [
                'adminUser' => $user,
                'localeCode' => $localCode,
            ],
        );
    }
}
