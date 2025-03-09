<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\Attribute\AttributeType;

use Owl\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class IntegerAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'integer';

    /**
     * @return 'integer'
     */
    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_INTEGER;
    }

    /**
     * @return 'integer'
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    public function validate(
        AttributeValueInterface $attributeValue,
        ExecutionContextInterface $context,
        array $configuration,
    ): void {
        if (!isset($configuration['required'])) {
            return;
        }

        $value = $attributeValue->getValue();

        foreach ($this->getValidationErrors($context, $value) as $error) {
            $context
                ->buildViolation($error->getMessage())
                ->atPath('value')
                ->addViolation()
            ;
        }
    }

    private function getValidationErrors(ExecutionContextInterface $context, ?int $value): ConstraintViolationListInterface
    {
        return $context->getValidator()->validate($value, [new NotBlank([])]);
    }
}
