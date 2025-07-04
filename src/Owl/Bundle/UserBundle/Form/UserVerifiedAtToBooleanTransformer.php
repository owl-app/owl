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

namespace Owl\Bundle\UserBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<mixed, mixed>
 */
final class UserVerifiedAtToBooleanTransformer implements DataTransformerInterface
{
    public function transform($value): mixed
    {
        return (bool) $value;
    }

    public function reverseTransform($value): mixed
    {
        return $value ? new \DateTime() : null;
    }
}
