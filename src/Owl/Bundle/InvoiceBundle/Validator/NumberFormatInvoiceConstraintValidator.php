<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Owl\Component\Invoice\Model\InvoiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class NumberFormatInvoiceConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var NumberFormatInvoiceConstraint $constraint */
        Assert::isInstanceOf($constraint, NumberFormatInvoiceConstraint::class);

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (str_starts_with($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        /** @var InvoiceInterface|null $validatedSerie */
        $validatedInvoice = $this->context->getObject();

        if (!$validatedInvoice->getSerie() && empty($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
