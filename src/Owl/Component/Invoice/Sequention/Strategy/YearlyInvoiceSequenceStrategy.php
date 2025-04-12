<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Factory\InvoiceSequenceFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceSequenceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class YearlyInvoiceSequenceStrategy implements InvoiceSequenceStrategyInterface
{
    public function __construct(
        private readonly RepositoryInterface $sequenceRepository,
        private readonly InvoiceSequenceFactoryInterface $invoiceSequenceFactory
    ) {}

    public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): InvoiceSequenceInterface
    {
        $year = (int) $date->format('Y');

        $sequence = $this->sequenceRepository->findOneBy([
            'year' => $year,
            'serie' => $invoiceSerie
        ]);

        if (!$sequence) {
            return $this->invoiceSequenceFactory->create($invoiceSerie, $year);
        }

        return $sequence;
    }

    public function incrementNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): InvoiceSequenceInterface
    {
        $sequence = $this->getNextCounter($invoiceSerie, $date);

        $sequence->incrementNextCounter();

        return $sequence;
    }
}