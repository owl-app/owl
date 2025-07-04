<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;

/**
 * @extends DateBasedInvoiceSequenceStrategy<SequenceInterface>
 */
class YearlyInvoiceSequenceStrategy extends DateBasedInvoiceSequenceStrategy
{
    public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): SequenceInterface
    {
        $year = (int) $date->format('Y');

        /** @var SequenceInterface|null $sequence */
        $sequence = $this->sequenceRepository->findOneBy([
            'year' => $year,
            'serie' => $invoiceSerie,
        ]);

        if (!$sequence) {
            $sequence = $this->invoiceSequenceFactory->create($invoiceSerie, $year);
        }

        return $sequence;
    }
}