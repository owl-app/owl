<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Owl\Component\Location\Model\ProvinceInterface;
use Owl\Component\Location\Repository\ProvinceRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @implements ProvinceRepositoryInterface<ProvinceInterface>
 */
class ProvinceRepository extends EntityRepository implements ProvinceRepositoryInterface
{
}
