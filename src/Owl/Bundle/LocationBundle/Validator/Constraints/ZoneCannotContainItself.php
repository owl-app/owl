<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ZoneCannotContainItself extends Constraint
{
    public string $message = 'owl.zone_member.cannot_be_the_same_as_zone';

    public function validatedBy(): string
    {
        return 'owl_zone_cannot_contain_itself_validator';
    }
}
