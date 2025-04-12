<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Sequention\Strategy;

use Owl\Component\Invoice\Model\InvoiceSequenceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;

interface InvoiceSequenceStrategyInterface
{
    public function getNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): InvoiceSequenceInterface;

    public function incrementNextCounter(InvoiceSerieInterface $invoiceSerie, \DateTimeInterface $date): InvoiceSequenceInterface;
}
