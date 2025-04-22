<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueProvinceCollection extends Constraint
{
    public string $message = 'owl.country.unique_provinces';

    public function validatedBy(): string
    {
        return 'owl_unique_province_collection_validator';
    }
}
