<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Owl\Component\Location\Model\ZoneMemberInterface;
use Owl\Component\Location\Repository\ZoneMemberRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @implements ZoneMemberRepositoryInterface<ZoneMemberInterface>
 */
class ZoneMemberRepository extends EntityRepository implements ZoneMemberRepositoryInterface
{
}
