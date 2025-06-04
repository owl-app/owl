<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Repository\CountryRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @implements CountryRepositoryInterface<CountryInterface>
 */
class CountryRepository extends EntityRepository implements CountryRepositoryInterface
{
}
