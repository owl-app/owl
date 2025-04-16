<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Generator;

class InvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(string $format, int $number, \DateTimeInterface $date): string
    {
        $search  = ['__YYYY__', '__MM__', '__NUMBER__'];
        $replace = [$date->format('Y'), $date->format('m'), $number];

        return str_replace($search, $replace, $format);
    }
}