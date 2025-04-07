<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Mailer;

interface Emails
{
    public const ADMIN_USER_REGISTRATION = 'admin_user_registration';

    public const ADMIN_PASSWORD_RESET = 'admin_password_reset';

    public const ADMIN_USER_REGISTRATION_ACCEPTED = 'admin_user_registration_accepted';

    public const ADMIN_USER_REGISTRATION_REJECTED = 'admin_user_registration_rejected';
}
