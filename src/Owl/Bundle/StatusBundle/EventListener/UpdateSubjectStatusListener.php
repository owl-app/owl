<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Owl\Component\Status\Model\StatusableInterface;
use Owl\Component\Status\Model\StatusInterface;

final class UpdateSubjectStatusListener
{
    /** @var ObjectManager */
    private $statusSubjectManager;

    public function __construct(ObjectManager $statusSubjectManager)
    {
        $this->statusSubjectManager = $statusSubjectManager;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->saveStatus($args);
    }

    public function saveStatus(LifecycleEventArgs $args): void
    {
        /** @var StatusInterface $subject */
        $subject = $args->getObject();

        if (!$subject instanceof StatusInterface) {
            return;
        }

        /** @var StatusableInterface $statusSubject */
        $statusSubject = $subject->getStatusSubject();
        $statusSubject->setStatus($subject->getStatus());

        $this->statusSubjectManager->flush();
    }
}
