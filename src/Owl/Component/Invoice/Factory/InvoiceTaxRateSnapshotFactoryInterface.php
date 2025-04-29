<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of TaxRateSnapshotInterface
 *
 * @extends FactoryInterface<T>
 */
interface InvoiceTaxRateSnapshotFactoryInterface extends FactoryInterface
{
    public function create(
        string $code,
        string $name,
        float $amount,
        
    ): TaxRateSnapshotInterface;
}
