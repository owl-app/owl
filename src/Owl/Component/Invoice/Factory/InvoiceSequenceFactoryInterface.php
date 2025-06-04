<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of SequenceInterface
 *
 * @extends FactoryInterface<T>
 */
interface InvoiceSequenceFactoryInterface extends FactoryInterface
{
    public function create(
        InvoiceSerieInterface $serie,
        int $year,
        int|null $month = null,
        int $nextCounter = 1,
    ): SequenceInterface;
}
