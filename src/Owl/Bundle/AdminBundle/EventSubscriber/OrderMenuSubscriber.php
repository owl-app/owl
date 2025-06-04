<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\EventSubscriber;

use Owl\Bundle\AdminBundle\Event\OrderMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OrderMenuSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     * @return array{'owl.admin.menu.order': 'setOrder'}
     */
    public static function getSubscribedEvents()
    {
        return [
            OrderMenuEvent::EVENT_NAME => 'setOrder',
        ];
    }

    public function setOrder(OrderMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        foreach ($order as $key => $name) {
            if (null === $menu->getChild($name)) {
                unset($order[$key]);
            }
        }

        $menu->reorderChildren($order);
    }
}
