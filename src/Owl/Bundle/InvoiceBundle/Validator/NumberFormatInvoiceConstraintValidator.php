<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Owl\Component\Invoice\Model\InvoiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NumberFormatInvoiceConstraintValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
    ) {
        $this->translator = $translator;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
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
            $context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}