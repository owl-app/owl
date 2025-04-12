<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Generator;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;

interface InvoiceNumberGeneratorInterface
{
    public function generate(InvoiceSerieInterface $invoiceSerie, int $number, \DateTimeInterface $date): string;
}
