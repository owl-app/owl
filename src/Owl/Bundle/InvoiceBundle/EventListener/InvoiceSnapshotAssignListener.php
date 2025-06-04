<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class InvoiceSnapshotAssignListener
{
    public function __construct(
        private PrioritizedServiceRegistryInterface $registrySnapshotAssigner,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assignSnapshot(GenericEvent $event): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $event->getSubject();

        Assert::isInstanceOf($invoice, InvoiceInterface::class);

        /** @var SnapshotAssignerInterface $snapshotAssigner */
        foreach ($this->registrySnapshotAssigner->all() as $snapshotAssigner) {
            $snapshotAssigner->assign($invoice);
        }
    }
}
