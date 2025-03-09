<?php

declare(strict_types=1);

namespace Owl\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Owl\Behat\NotificationType;
use Owl\Behat\Page\Admin\Account\LoginPageInterface;
use Owl\Behat\Page\Admin\DashboardPageInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    public function __construct(
        private DashboardPageInterface $dashboardPage,
        private LoginPageInterface $loginPage,
    ) {
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn()
    {
        $this->loginPage->open();
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername($username = null)
    {
        $this->loginPage->specifyUsername($username);
    }

    /**
     * @When I specify the password as :password
     * @When I do not specify the password
     */
    public function iSpecifyThePasswordAs($password = null)
    {
        $this->loginPage->specifyPassword($password);
    }

    /**
     * @When I log in
     */
    public function iLogIn()
    {
        $this->loginPage->logIn();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->requestPasswordResetPage->open();
    }

    /**
     * @When I specify email as :email
     */
    public function iSpecifyEmailAs(string $email): void
    {
        $this->requestPasswordResetPage->specifyEmail($email);
    }

    /**
     * @When I reset it
     */
    public function iResetIt(): void
    {
        $this->requestPasswordResetPage->resetPassword();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        $this->dashboardPage->open();
        Assert::true($this->dashboardPage->isOpen());
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        Assert::false($this->dashboardPage->isOpen());
    }

    /**
     * @Given I should be on login page
     */
    public function iShouldBeOnLoginPage()
    {
        Assert::true($this->loginPage->isOpen());
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials()
    {
        Assert::true($this->loginPage->hasValidationErrorWith('Error Invalid credentials.'));
    }

    /**
     * @Then I should be able to log in as :username authenticated by :password password
     */
    public function iShouldBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        $this->dashboardPage->verify();
    }

    /**
     * @When /^(this administrator) logs in using "([^"]+)" password$/
     */
    public function theyLogIn(AdminUserInterface $adminUser, $password)
    {
        $this->logInAgain($adminUser->getUsername(), $password);
    }

    /**
     * @Then I should not be able to log in as :username authenticated by :password password
     */
    public function iShouldNotBeAbleToLogInAsAuthenticatedByPassword($username, $password)
    {
        $this->logInAgain($username, $password);

        Assert::true($this->loginPage->hasValidationErrorWith('Error Invalid credentials.'));
        Assert::false($this->dashboardPage->isOpen());
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionHasBeenSent(): void
    {
        $this->notificationChecker->checkNotification(
            'If the email you have specified exists in our system, we have sent there an instruction on how to reset your password.',
            NotificationType::success(),
        );
    }

    /**
     * @param string $username
     * @param string $password
     */
    private function logInAgain($username, $password)
    {
        $this->dashboardPage->open();
        $this->dashboardPage->logOut();

        $this->loginPage->open();
        $this->loginPage->specifyUsername($username);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
    }

    /**
     * @Then I should be on the login page
     */
    public function iShouldBeOnTheLoginPage(): void
    {
        Assert::true($this->loginPage->isOpen());
    }
}
