<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;

class MonthlyInvoiceSequenceStrategy extends DateBasedInvoiceSequenceStrategy
{
    public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): SequenceInterface
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');

        $sequence = $this->sequenceRepository->findOneBy([
            'year' => $year,
            'month' => $month,
            'serie' => $invoiceSerie,
        ]);

        if (!$sequence) {
            return $this->invoiceSequenceFactory->create($invoiceSerie, $year, $month);
        }

        return $sequence;
    }
}