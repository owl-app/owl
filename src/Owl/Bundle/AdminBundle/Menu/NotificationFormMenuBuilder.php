<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class NotificationFormMenuBuilder
{
    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('notification', $options) || !$options['notification'] instanceof NotificationInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@OwlAdmin/Notification/Tab/_details.html.twig')
            ->setLabel('owl.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('files')
            ->setAttribute('template', '@OwlAdmin/Notification/Tab/_files.html.twig')
            ->setLabel('owl.ui.files')
        ;

        return $menu;
    }
}
