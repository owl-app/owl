<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Updater\SingleRoleUpdaterInterface;
use Webmozart\Assert\Assert;

final class UserRoleUpdateListener
{
    public function __construct(private SingleRoleUpdaterInterface $roleUpdater)
    {
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function postPersist(AdminUserInterface $adminUser, LifecycleEventArgs $event): void
    {
        if ($adminUser->getRole()) {
            $this->roleUpdater->assign($adminUser);
        }
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function postUpdate(AdminUserInterface $adminUser, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);

        $unitOfWork = $entityManager->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($adminUser);

        if (isset($changeSet['role'])) {
            $this->roleUpdater->assign($adminUser);
        }
    }
}
