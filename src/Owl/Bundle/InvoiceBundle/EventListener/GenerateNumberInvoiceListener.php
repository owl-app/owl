<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class GenerateNumberInvoiceListener
{
    public function __construct(
        private InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
    ) {
    }

    public function preCreate(GenericEvent $event): void
    {
        $invoice = $event->getSubject();

        /** @var BaseInvoiceInterface $invoice */
        Assert::isInstanceOf($invoice, BaseInvoiceInterface::class);

        /** @var InvoiceSerieInterface $serie */
        $serie = $invoice->getSerie();

        if (empty($serie)) {
            return;
        }

        $invoice->setFullNumber($this->invoiceNumberGenerator->generate(
            $serie->getFormat(),
            $invoice->getSequenceNumber(),
            $invoice->getIssueDate())
        );
    }
}
