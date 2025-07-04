<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\Authorization\OwnerableUserInterface;
use Owl\Component\User\Model\UserAwareInterface;

final class AddOwnerSubscriber implements EventSubscriber
{
    private AdminUserContextInterface $adminUserContext;

    public function __construct(AdminUserContextInterface $adminUserContext)
    {
        $this->adminUserContext = $adminUserContext;
    }

    /**
     * @return list{'prePersist'}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof OwnerableUserInterface && $entity instanceof UserAwareInterface && null === $entity->getUser()) {
            $entity->setUser($this->adminUserContext->getUser());
        }
    }
}