<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of InvoiceFactoryInterface
 *
 * @extends FactoryInterface<T>
 */
interface InvoiceFactoryInterface extends FactoryInterface
{
    public function createWithDefaults(): BaseInvoiceInterface;
}
