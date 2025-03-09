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

namespace Owl\Behat\Page\Admin\Account;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Owl\Behat\Service\DriverHelper;
use Owl\Behat\Service\JQueryHelper;

class LoginPage extends SymfonyPage implements LoginPageInterface
{
    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('validation_error')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getDocument()->pressButton('Login');

        if (DriverHelper::isJavascript($this->getDriver())) {
            JQueryHelper::waitForFormToStopLoading($this->getDocument());
        }
    }

    public function specifyPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    public function specifyUsername(string $username): void
    {
        $this->getDocument()->fillField('Username', $username);
    }

    public function getRouteName(): string
    {
        return 'owl_admin_login';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'validation_error' => '.message.negative',
        ]);
    }
}
