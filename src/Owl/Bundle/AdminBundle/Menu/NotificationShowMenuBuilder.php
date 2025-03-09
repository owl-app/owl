<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Owl\Component\Notification\Model\NotificationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class NotificationShowMenuBuilder
{
    public const EVENT_NAME = 'owl.menu.admin.notification.show';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!isset($options['notification'])) {
            return $menu;
        }

        $notification = $options['notification'];
        $this->addChildren($menu, $notification);

        return $menu;
    }

    private function addChildren(ItemInterface $menu, NotificationInterface $notification): void
    {
        $menu->setExtra('column_id', 'actions');

        $menu
            ->addChild('update', [
                'route' => 'owl_admin_notification_update',
                'routeParameters' => ['id' => $notification->getId()],
            ])
            ->setAttribute('type', 'edit')
            ->setAttribute('icon', 'pencil')
            ->setLabel('owl.ui.edit')
        ;

        $menu
            ->addChild('user_delete', [
                'route' => 'owl_admin_notification_delete',
                'routeParameters' => ['id' => $notification->getId()],
            ])
            ->setAttribute('type', 'delete')
            ->setAttribute('resource_id', $notification->getId())
            ->setLabel('owl.ui.delete')
        ;
    }
}
