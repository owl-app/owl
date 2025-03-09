<?php

declare(strict_types=1);

namespace Owl\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface DashboardPageInterface extends SymfonyPageInterface
{
    public function getTotalSales(): string;

    public function getNumberOfNewOrders(): int;

    public function getNumberOfNewOrdersInTheList(): int;

    public function getNumberOfNewCustomers(): int;

    public function getNumberOfNewCustomersInTheList(): int;

    public function getAverageOrderValue(): string;

    public function getSubHeader(): string;

    public function isSectionWithLabelVisible(string $name): bool;

    public function logOut(): void;

    public function chooseChannel(string $channelName): void;
}
