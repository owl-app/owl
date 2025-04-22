<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Validator\Constraints;

use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ZoneCannotContainItselfValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        /** @var ZoneCannotContainItself $constraint */
        Assert::isInstanceOf($constraint, ZoneCannotContainItself::class);

        /** @var ZoneMemberInterface $zoneMember */
        foreach ($value as $zoneMember) {
            $zone = $zoneMember->getBelongsTo();

            if ($zone->getType() !== ZoneInterface::TYPE_ZONE) {
                continue;
            }

            if ($zoneMember->getCode() === $zone->getCode()) {
                $this->context->addViolation($constraint->message);
            }
        }
    }
}
