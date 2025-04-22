<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Location\Model\ProvinceInterface;
use Owl\Component\Location\Repository\ProvinceRepositoryInterface;

/**
 * @implements ProvinceRepositoryInterface<ProvinceInterface>
 */
class ProvinceRepository extends EntityRepository implements ProvinceRepositoryInterface
{
}
