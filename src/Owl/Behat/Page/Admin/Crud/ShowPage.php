<?php

declare(strict_types=1);

namespace Owl\Behat\Page\Admin\Crud;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Owl\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private TableAccessorInterface $tableAccessor,
        private string $routeName,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRowDetailsValueByIndex(int $columnIndex): ?string
    {
        try {
            $table = $this->getElement('table');
            $row = $table->findAll('css', 'tr')[$columnIndex];
            $columns = $row->findAll('css', 'th,td');

            return $columns[1]->getText();
        } catch (\InvalidArgumentException) {
            return null;
        } catch (ElementNotFoundException) {
            return null;
        }
    }

    protected function getTableDetailRows(int $row)
    {
        $table = $this->getElement('table');

        return $table->findAll('css', 'tr');
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'table' => '.table',
        ]);
    }
}
