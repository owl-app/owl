<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Symfony\Component\Validator\Constraint;

class NumberFormatInvoiceConstraint extends Constraint
{
    /** @var string */
    public $message = 'owl.invoice.number.format.valid';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'owl_number_format_invoice_validator';
    }
}
