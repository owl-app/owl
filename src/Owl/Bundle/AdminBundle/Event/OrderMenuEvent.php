<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Owl\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class OrderMenuEvent extends MenuBuilderEvent
{
    public const EVENT_NAME = 'owl.admin.menu.order';

    public function __construct(FactoryInterface $factory, ItemInterface $menu, private array $order)
    {
        parent::__construct($factory, $menu);
    }

    public function getOrder(): array
    {
        return $this->order;
    }
}
