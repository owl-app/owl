<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Location\Model\ZoneMemberInterface;
use Owl\Component\Location\Repository\ZoneMemberRepositoryInterface;

/**
 * @implements ZoneMemberRepositoryInterface<ZoneMemberInterface>
 */
class ZoneMemberRepository extends EntityRepository implements ZoneMemberRepositoryInterface
{
}
