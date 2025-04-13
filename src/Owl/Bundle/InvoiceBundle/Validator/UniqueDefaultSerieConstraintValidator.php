<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Validator;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UniqueDefaultSerieConstraintValidator extends ConstraintValidator
{
    public function __construct(private RepositoryInterface $invoiceSequenceRepository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var UniqueDefaultSerieConstraint $constraint */
        Assert::isInstanceOf($constraint, UniqueDefaultSerieConstraint::class);

        if ($value !== true) {
            return;
        }

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (str_starts_with($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        /** @var InvoiceSerieInterface|null $validatedSerie */
        $validatedSerie = $this->context->getObject();
        /** @var InvoiceSerieInterface|null $serie */
        $serie = $this->invoiceSequenceRepository->findOneBy(['isDefault' => true, 'invoiceType' => $validatedSerie->getInvoiceType()]);

        if ($serie !== null && $validatedSerie->getId() !== $serie->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ format }}', $serie->getFormat())
                ->addViolation()
            ;
        }
    }
}
