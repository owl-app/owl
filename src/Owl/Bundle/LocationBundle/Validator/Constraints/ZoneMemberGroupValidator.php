<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Owl\Component\Location\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ZoneMemberGroupValidator extends ConstraintValidator
{
    /** @param array<string, array<string, string>> $validationGroups */
    public function __construct(private readonly array $validationGroups)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ZoneMemberGroup) {
            throw new UnexpectedTypeException($constraint, ZoneMemberGroup::class);
        }

        if (!$value instanceof ZoneMemberInterface) {
            throw new UnexpectedValueException($value, ZoneMemberInterface::class);
        }

        /** @var string[] $groups */
        $groups = $this->validationGroups[$value->getBelongsTo()?->getType()] ?? $constraint->groups;
        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate(value: $value, groups: $groups);
    }
}
