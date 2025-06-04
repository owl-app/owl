<?php

declare(strict_types=1);

namespace Owl\Component\Location\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Model\ZoneInterface as ModelZoneInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ZoneRepositoryInterface extends RepositoryInterface
{
    public function findOneByAddressAndType(ProvinceCodeAwareInterface&CountryCodeAwareInterface $location, string $type): ?ModelZoneInterface;

    /** @return ZoneInterface[] */
    public function findByAddress(ProvinceCodeAwareInterface&CountryCodeAwareInterface $location): array;

    /** @return ZoneInterface[] */
    public function findAllByCountryAndProvince(?string $countryCode, ?string $provinceCode): array;

    public function createByCountryAndProvince(?string $countryCode, ?string $provinceCode): QueryBuilder;

    /**
     * @param array<ZoneInterface> $members
     *
     * @return array<ZoneInterface>
     */
    public function findByMembers(array $members): array;
}
