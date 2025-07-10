<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Validator\Constraints;

use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Repository\CountryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class RequiredProvinceCodeValidator extends ConstraintValidator
{
    /**
     * @param CountryRepositoryInterface<CountryInterface> $countryRepository
     */
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var RequiredProvinceCode $constraint */
        Assert::isInstanceOf($constraint, RequiredProvinceCode::class);

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (str_starts_with($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        /** @var (ProvinceCodeAwareInterface&CountryCodeAwareInterface)|null $validated */
        $validated = $this->context->getObject();

        Assert::isInstanceOf($validated, ProvinceCodeAwareInterface::class);
        Assert::isInstanceOf($validated, CountryCodeAwareInterface::class);

        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $validated->getCountryCode()]);

        if (!empty($validated->getCountryCode()) && $country?->hasProvinces() && empty($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
