<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Owl\Component\Invoice\Provider\InvoiceSerieProviderInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of BaseInvoiceInterface
 *
 * @implements InvoiceFactoryInterface<T>
 */
final class InvoiceFactory implements InvoiceFactoryInterface
{
    /** @param FactoryInterface<T> $decoratedFactory */
    public function __construct(
        private FactoryInterface $decoratedFactory,
        private ServiceRegistryInterface $registryInvoiceSequenceStrategy,
        private InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        private InvoiceSerieProviderInterface $invoiceSerieProvider,
    ) {
    }

    public function createNew(): BaseInvoiceInterface
    {
        return $this->decoratedFactory->createNew();
    }

    /** @inheritdoc */
    public function createWithDefaults(?string $type): BaseInvoiceInterface
    {
        $now = new \DateTimeImmutable();
        $defaultSerie = $this->invoiceSerieProvider->getSerie($type);
        /** @var InvoiceSequenceStrategyInterface $incrementStrategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($defaultSerie->getSequenceIncrement());
        $invoiceSequence = $strategy->getNextCounter($defaultSerie, $now);

        $invoice = $this->decoratedFactory->createNew();
        $invoice->setSequenceNumber($invoiceSequence->getNextCounter());
        $invoice->setFullNumber($this->invoiceNumberGenerator->generate($defaultSerie, $invoiceSequence->getNextCounter(), $now));
        $invoice->setType($type);
        $invoice->setIssueDate($now);
        $invoice->setSerie($defaultSerie);

        return $invoice;
    }
}
