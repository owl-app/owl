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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class DateAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'date';

    /**
     * @return 'date'
     */
    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_DATE;
    }

    /**
     * @return 'date'
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
    }
}
