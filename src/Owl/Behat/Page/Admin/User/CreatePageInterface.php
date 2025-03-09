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

namespace Owl\Behat\Page\Admin\User;

use Owl\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable(): void;

    public function specifyDisplayName(string $displayName): void;

    public function specifyEmail(string $email): void;

    public function specifyPassword(string $password): void;

    public function specifyLocale(string $localeCode): void;
}
