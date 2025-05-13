<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of TaxRateInterface
 *
 * @extends RepositoryInterface<T>
 */
interface InvoiceTaxRateRepositoryInterface extends RepositoryInterface
{
    public function findByZone(ZoneInterface $zone): array;
}
