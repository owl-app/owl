<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class RequiredProvinceCode extends Constraint
{
    public string $message = 'owl.address.province.required';

    public function validatedBy(): string
    {
        return 'owl_address_province_code_required';
    }

    public function getTargets(): string
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
