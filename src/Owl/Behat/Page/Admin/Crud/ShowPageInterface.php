<?php

declare(strict_types=1);

namespace Owl\Behat\Page\Admin\Crud;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function getRowDetailsValueByIndex(int $columnIndex): ?string;
}
