<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueDefaultSerieConstraint extends Constraint
{
    /** @var string */
    public $message = 'owl.invoice.serie.default.valid';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'owl_unique_default_serie_validator';
    }
}
