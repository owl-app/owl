<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Model;

use PHPUnit\Framework\TestCase;
use Owl\Component\Locale\Model\Locale;
use Owl\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

final class LocaleTest extends TestCase
{
    private Locale $locale;

    protected function setUp(): void
    {
        $this->locale = new Locale();
    }

    public function testImplementsLocaleInterface(): void
    {
        self::assertInstanceOf(LocaleInterface::class, $this->locale);
    }

    public function testTimestampable(): void
    {
        self::assertInstanceOf(TimestampableInterface::class, $this->locale);
    }

    public function testDoesNotHaveIdByDefault(): void
    {
        self::assertNull($this->locale->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->locale->getCode());
    }

    public function testCodeIsMutable(): void
    {
        $this->locale->setCode('en_US');
        self::assertSame('en_US', $this->locale->getCode());
    }

    public function testHasAName(): void
    {
        $this->locale->setCode('en_US');
        self::assertSame('English (United States)', $this->locale->getName());
        self::assertSame('inglés (Estados Unidos)', $this->locale->getName('es'));

        $this->locale->setCode('en');
        self::assertSame('English', $this->locale->getName());
        self::assertSame('inglés', $this->locale->getName('es'));
    }

    public function testReturnsNameWhenConvertedToString(): void
    {
        $this->locale->setCode('en_US');
        self::assertSame('English (United States)', $this->locale->__toString());

        $this->locale->setCode('en');
        self::assertSame('English', $this->locale->__toString());
    }

    public function testHasCreatedAtByDefault(): void
    {
        self::assertInstanceOf(\DateTime::class, $this->locale->getCreatedAt());
    }

    public function testDoesNotHaveUpdatedAtByDefault(): void
    {
        self::assertNull($this->locale->getUpdatedAt());
    }
} 