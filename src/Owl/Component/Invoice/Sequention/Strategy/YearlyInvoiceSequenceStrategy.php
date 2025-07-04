<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Sylius\Resource\Model\ResourceInterface;

class YearlyInvoiceSequenceStrategy extends DateBasedInvoiceSequenceStrategy
{
    public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): SequenceInterface
    {
        $year = (int) $date->format('Y');

        $sequence = $this->sequenceRepository->findOneBy([
            'year' => $year,
            'serie' => $invoiceSerie,
        ]);

        if (!$sequence) {
            /** @var SequenceInterface */
            $sequence = $this->invoiceSequenceFactory->create($invoiceSerie, $year);
        }

        return $sequence;
    }
}