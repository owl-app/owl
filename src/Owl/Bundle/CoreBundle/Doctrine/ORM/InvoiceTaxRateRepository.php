<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Owl\Component\Core\Repository\InvoiceTaxRateRepositoryInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of TaxRateInterface
 *
 * @implements InvoiceTaxRateRepositoryInterface<T>
 */
class InvoiceTaxRateRepository extends EntityRepository implements InvoiceTaxRateRepositoryInterface
{
    public function findByZone(ZoneInterface $zone): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.zones', 'zones')
            ->where('zones.id = :zonesId')
            ->setParameter('zonesId', $zone)
            ->orderBy('o.amount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
