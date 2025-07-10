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

namespace Owl\Bundle\UserBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Owl\Component\User\Model\UserInterface;
use Owl\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PasswordUpdaterListener
{
    /** @var PasswordUpdaterInterface */
    private $passwordUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
    }

    public function genericEventUpdater(GenericEvent $event): void
    {
        /** @var UserInterface $subject */
        $subject = $event->getSubject();
        $this->updatePassword($subject);
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }

        $this->updatePassword($user);
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }

        $this->updatePassword($user);
    }

    protected function updatePassword(UserInterface $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $this->passwordUpdater->updatePassword($user);
        }
    }
}
