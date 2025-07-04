<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Doctrine\ORM;

use Owl\Component\Setting\Model\SettingInterface;
use Owl\Component\Setting\Repository\SettingRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of SettingInterface
 */
class SettingRepository extends EntityRepository implements SettingRepositoryInterface
{
    /**
     * @return array<T>
     */
    public function finAllBySection(string $section): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.section = :section')
            ->setParameter('section', $section)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array<string> $keys
     * @return array<T>
     */
    public function finAllBySectionAndKeys(string $section, array $keys): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.section = :section')
            ->andWhere('o.name IN (:keys)')
            ->setParameter('section', $section)
            ->setParameter('keys', $keys)
            ->getQuery()
            ->getResult()
        ;
    }
}