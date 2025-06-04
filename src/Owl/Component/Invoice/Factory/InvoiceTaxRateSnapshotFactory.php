<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of TaxRateSnapshotInterface
 *
 * @implements InvoiceTaxRateSnapshotFactoryInterface<T>
 */
final class InvoiceTaxRateSnapshotFactory implements InvoiceTaxRateSnapshotFactoryInterface
{
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
        string $code,
        string $name,
        float $amount,
    ): TaxRateSnapshotInterface {
        $snapshot = $this->decoratedFactory->createNew();
        $snapshot->setCode($code);
        $snapshot->setName($name);
        $snapshot->setAmount($amount);

        return $snapshot;
    }
}
