<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Owl\Component\Invoice\Model\InvoiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class NumberFormatInvoiceConstraintValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
    ) {
        $this->translator = $translator;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var NumberFormatInvoiceConstraint $constraint */
        Assert::isInstanceOf($constraint, NumberFormatInvoiceConstraint::class);

        $context = $this->context;
        Assert::isInstanceOf($context, ExecutionContextInterface::class);

        $propertyPath = $context->getPropertyPath();

        /** @var ConstraintViolationListInterface $violations */
        $violations = $context->getViolations();
        foreach (iterator_to_array($violations) as $violation) {
            if (str_starts_with($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        /** @var InvoiceInterface|null $validatedInvoice */
        $validatedInvoice = $context->getObject();
        if (!$validatedInvoice instanceof InvoiceInterface || (null === $validatedInvoice->getSerie() && empty($value))) {
            $context->buildViolation($this->translator->trans($constraint->message))
                ->addViolation();
        }
    }
}
