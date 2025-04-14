<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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

        /** @var BaseInvoiceInterface|null $validatedSerie */
        $validatedInvoice = $this->context->getObject();

        if (!$format = $validatedInvoice->getSerie()?->getFormat()) {
            return;
        }

        $escaped = preg_quote($format, '/');

        $regexParts = [
            '__NUMBER__' => '\d+',
            '__MM__' => '(0[1-9]|1[0-2])',
            '__YYYY__' => '\d{4}',
        ];

        $regex = '/^' . str_replace(array_keys($regexParts), array_values($regexParts), $escaped) . '$/';

        if (!preg_match($regex, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ format }}', $this->translationFormat($format, $validatedInvoice))
                ->addViolation()
            ;
        }
    }

    private function translationFormat(string $format, BaseInvoiceInterface $invoice): string
    {
        $date = $invoice->getIssueDate();
        $number = $invoice->getNumber();

        $search  = ['__YYYY__', '__MM__', '__NUMBER__'];
        $replace = [
            $date?->format('Y') ?? $this->translator->trans('owl.invoice.number.text_year', [], 'validators'),
            $date?->format('m') ?? $this->translator->trans('owl.invoice.number.text_month', [], 'validators'),
            $number ?? $this->translator->trans('owl.invoice.number.text_number', [], 'validators')
        ];

        return str_replace($search, $replace, $format);
    }
}
