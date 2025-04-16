<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Generator;

interface InvoiceNumberGeneratorInterface
{
    public function generate(string $format, int $number, \DateTimeInterface $date): string;
}
