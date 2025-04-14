<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class InvoiceSequenceIncrementListener
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRegistryInterface $registryInvoiceSequenceStrategy,
    ) {
    }

    public function preCreate(GenericEvent $event): void
    {
        $invoice = $event->getSubject();

        /** @var BaseInvoiceInterface $invoice */
        Assert::isInstanceOf($invoice, BaseInvoiceInterface::class);

        $serie = $invoice->getSerie();

        if (empty($serie)) {
            return;
        }

        /** @var InvoiceSequenceStrategyInterface $strategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($serie->getSequenceIncrement());
        $invoiceSequence = $strategy->updateCounter($serie, $invoice->getSequenceNumber(), $invoice->getIssueDate());

        $this->manager->persist($invoiceSequence);
    }
}
