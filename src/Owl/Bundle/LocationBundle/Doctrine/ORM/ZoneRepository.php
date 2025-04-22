<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Repository\ZoneRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @implements ZoneRepositoryInterface<ZoneInterface>
 */
class ZoneRepository extends EntityRepository implements ZoneRepositoryInterface
{
    public function findOneByAddressAndType(ProvinceCodeAwareInterface&CountryCodeAwareInterface $location, string $type): ?ZoneInterface
    {
        $queryBuilder = $this->createByAddressQueryBuilder($location);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.type', ':type'))
            ->setParameter('type', $type)
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /** @return ZoneInterface[] */
    public function findByAddress(ProvinceCodeAwareInterface&CountryCodeAwareInterface $location): array
    {
        return $this->createByAddressQueryBuilder($location)->getQuery()->getResult();
    }

    public function createByAddressQueryBuilder(ProvinceCodeAwareInterface&CountryCodeAwareInterface $location): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        $orConditions = [];

        if ($location->getCountryCode() !== null) {
            $orConditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('o.type', ':country'),
                $queryBuilder->expr()->eq('members.code', ':countryCode'),
            );

            $queryBuilder->setParameter('country', ZoneInterface::TYPE_COUNTRY);
            $queryBuilder->setParameter('countryCode', $location->getCountryCode());
        }

        if ($location->getProvinceCode() !== null) {
            $orConditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('o.type', ':province'),
                $queryBuilder->expr()->eq('members.code', ':provinceCode'),
            );

            $queryBuilder->setParameter('province', ZoneInterface::TYPE_PROVINCE);
            $queryBuilder->setParameter('provinceCode', $location->getProvinceCode());
        }

        if ($orConditions !== []) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(...$orConditions));
        }

        return $queryBuilder;
    }

    /**
     * @param array<ZoneInterface> $members
     *
     * @return array<ZoneInterface>
     */
    public function findByMembers(array $members): array
    {
        $zonesCodes = array_map(
            fn (ZoneInterface $zone): string => $zone->getCode(),
            $members,
        );

        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        $queryBuilder
            ->andWhere('o.type = :type')
            ->andWhere($queryBuilder->expr()->in('members.code', ':zones'))
            ->setParameter('type', ZoneInterface::TYPE_ZONE)
            ->setParameter('zones', $zonesCodes)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
