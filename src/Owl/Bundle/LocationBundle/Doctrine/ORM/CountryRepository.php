<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Repository\CountryRepositoryInterface;

/**
 * @implements CountryRepositoryInterface<CountryInterface>
 */
class CountryRepository extends EntityRepository implements CountryRepositoryInterface
{
}
