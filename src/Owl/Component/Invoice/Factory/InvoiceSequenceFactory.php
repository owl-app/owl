<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of SequenceInterface
 *
 * @implements InvoiceSequenceFactoryInterface<T>
 */
final class InvoiceSequenceFactory implements InvoiceSequenceFactoryInterface
{
    /**
     * @param FactoryInterface<SequenceInterface> $decoratedFactory
     */
    public function __construct(
        private FactoryInterface $decoratedFactory,
    ) {
    }

    /**
     * @throws UnsupportedMethodException
     */
    public function createNew(): object
    {
        throw new UnsupportedMethodException('createNew');
    }

    /** @inheritdoc */
    public function create(
        InvoiceSerieInterface $serie,
        int $year,
        ?int $month = null,
        int $nextCounter = 1,
    ): SequenceInterface {
        $sequence = $this->decoratedFactory->createNew();
        $sequence->setSerie($serie);
        $sequence->setYear($year);
        $sequence->setMonth($month);
        $sequence->setNextCounter($nextCounter);

        return $sequence;
    }
}
