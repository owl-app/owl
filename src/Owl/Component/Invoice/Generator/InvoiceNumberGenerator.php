<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Generator;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;

class InvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(InvoiceSerieInterface $invoiceSerie, int $number, \DateTimeInterface $date): string
    {
        $search  = ['{YYYY}', '{MM}', '{NUMBER}'];
        $replace = [$date->format('Y'), $date->format('m'), $number];

        return str_replace($search, $replace, $invoiceSerie->getFormat());
    }
}