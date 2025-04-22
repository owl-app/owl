<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ZoneMemberGroup extends Constraint
{
    public function validatedBy(): string
    {
        return 'owl_zone_member_group';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
