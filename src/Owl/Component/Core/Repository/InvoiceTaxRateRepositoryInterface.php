<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface InvoiceTaxRateRepositoryInterface extends RepositoryInterface
{
    /**
     * @return TaxRateInterface[]
     */
    public function findByZone(ZoneInterface $zone): array;
}
