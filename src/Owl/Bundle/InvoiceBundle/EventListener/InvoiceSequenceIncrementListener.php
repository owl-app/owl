<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Owl\Component\Invoice\Provider\InvoiceSerieProviderInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class InvoiceSequenceIncrementListener
{
    public function __construct(
        private EntityManagerInterface $manager,
        private InvoiceSerieProviderInterface $invoiceSerieProvider,
        private ServiceRegistryInterface $registryInvoiceSequenceStrategy,
    ) {
    }

    public function preCreate(GenericEvent $event): void
    {
        $invoice = $event->getSubject();

        /** @var BaseInvoiceInterface $invoice */
        Assert::isInstanceOf($invoice, BaseInvoiceInterface::class);

        $defaultSerie = $this->invoiceSerieProvider->getSerie($invoice->getType());
        /** @var InvoiceSequenceStrategyInterface $incrementStrategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($defaultSerie->getSequenceIncrement());
        $invoiceSequence = $strategy->incrementNextCounter($defaultSerie, $invoice->getIssueDate());

        $this->manager->persist($invoiceSequence);
    }
}
