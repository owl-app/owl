<?php

declare(strict_types=1);

namespace Owl\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Owl\Behat\Page\Admin\DashboardPageInterface;
use Webmozart\Assert\Assert;

final class DashboardContext implements Context
{
    public function __construct(private DashboardPageInterface $dashboardPage)
    {
    }

    /**
     * @Given I am on the administration dashboard
     * @When I (try to )open administration dashboard
     */
    public function iOpenAdministrationDashboard(): void
    {
        try {
            $this->dashboardPage->open();
        } catch (UnexpectedPageException) {
        }
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->dashboardPage->logOut();
    }

    /**
     * @Then I should not see the administration dashboard
     */
    public function iShouldNotSeeTheAdministrationDashboard(): void
    {
        Assert::false($this->dashboardPage->isOpen());
    }
}
