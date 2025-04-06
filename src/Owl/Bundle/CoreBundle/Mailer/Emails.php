<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Mailer;

interface Emails
{
    public const USER_REGISTRATION = 'user_registration';

    public const ADMIN_PASSWORD_RESET = 'admin_password_reset';

    public const ACCOUNT_VERIFICATION_TOKEN = 'account_verification_token';

    public const REGISTRATION_ACCEPTED = 'registration_accepted';

    public const REGISTRATION_REJECTED = 'registration_rejected';
}
