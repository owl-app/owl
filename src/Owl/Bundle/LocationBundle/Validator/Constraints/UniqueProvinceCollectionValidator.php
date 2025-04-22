<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use Owl\Component\Location\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UniqueProvinceCollectionValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var Collection<array-key, ProvinceInterface> $value */
        Assert::allIsInstanceOf($value, ProvinceInterface::class);
        /** @var UniqueProvinceCollection $constraint */
        Assert::isInstanceOf($constraint, UniqueProvinceCollection::class);

        if ($value->isEmpty()) {
            return;
        }

        $provincesWithAnyRequiredData = $value->filter(
            fn (ProvinceInterface $province): bool => null !== $province->getCode() || null !== $province->getName(),
        );

        $codes = [];
        $names = [];
        foreach ($provincesWithAnyRequiredData as $province) {
            $code = $province->getCode();
            $name = $province->getName();

            if (isset($code) && in_array($code, $codes) || isset($name) && in_array($name, $names)) {
                $this->context->addViolation($constraint->message);

                return;
            }

            $codes[] = $code;
            $names[] = $name;
        }
    }
}
