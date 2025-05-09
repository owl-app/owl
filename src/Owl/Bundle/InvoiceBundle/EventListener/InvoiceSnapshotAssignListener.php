<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class InvoiceSnapshotAssignListener
{
    public function __construct(
        private SnapshotAssignerInterface $taxRateSnapshotAssigner,
        private SnapshotAssignerInterface $buyerSnapshotAssigner,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assignSnapshot(GenericEvent $event): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $event->getSubject();

        $this->taxRateSnapshotAssigner->assign($invoice);
        $this->buyerSnapshotAssigner->assign($invoice);
    }
}
