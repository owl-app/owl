<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Factory\InvoiceSequenceFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

abstract class DateBasedInvoiceSequenceStrategy implements InvoiceSequenceStrategyInterface
{
    public function __construct(
        protected readonly RepositoryInterface $sequenceRepository,
        protected readonly InvoiceSequenceFactoryInterface $invoiceSequenceFactory,
    ) {
    }

    abstract public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): SequenceInterface;

    public function updateCounter(InvoiceSerieInterface $invoiceSerie, int $number, \DateTimeInterface $date): SequenceInterface
    {
        $sequence = $this->getNextCounter($invoiceSerie, $date);

        if ($sequence->getNextCounter() > $number) {
            return $sequence;
        }

        if ($sequence->getNextCounter() < $number) {
            $sequence->setNextCounter($number);
        }

        $sequence->incrementNextCounter();

        return $sequence;
    }
}
