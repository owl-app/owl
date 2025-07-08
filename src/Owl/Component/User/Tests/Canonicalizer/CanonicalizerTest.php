<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Canonicalizer;

use Owl\Component\User\Canonicalizer\Canonicalizer;
use Owl\Component\User\Canonicalizer\CanonicalizerInterface;
use PHPUnit\Framework\TestCase;

final class CanonicalizerTest extends TestCase
{
    private Canonicalizer $canonicalizer;

    protected function setUp(): void
    {
        $this->canonicalizer = new Canonicalizer();
    }

    public function testImplementsCanonicalizerInterface(): void
    {
        self::assertInstanceOf(CanonicalizerInterface::class, $this->canonicalizer);
    }

    public function testCanonicalizeConvertsToLowercase(): void
    {
        $result = $this->canonicalizer->canonicalize('TEST@EXAMPLE.COM');
        self::assertSame('test@example.com', $result);
    }

    public function testCanonicalizeHandlesAlreadyLowercaseString(): void
    {
        $result = $this->canonicalizer->canonicalize('test@example.com');
        self::assertSame('test@example.com', $result);
    }

    public function testCanonicalizeHandlesMixedCaseString(): void
    {
        $result = $this->canonicalizer->canonicalize('TeSt@ExAmPlE.CoM');
        self::assertSame('test@example.com', $result);
    }

    public function testCanonicalizeWithNullReturnsNull(): void
    {
        $result = $this->canonicalizer->canonicalize(null);
        self::assertNull($result);
    }

    public function testCanonicalizeWithEmptyString(): void
    {
        $result = $this->canonicalizer->canonicalize('');
        self::assertSame('', $result);
    }

    public function testCanonicalizeWithWhitespace(): void
    {
        $result = $this->canonicalizer->canonicalize('  TEST@EXAMPLE.COM  ');
        self::assertSame('  test@example.com  ', $result);
    }

    public function testCanonicalizeHandlesUnicodeCharacters(): void
    {
        $result = $this->canonicalizer->canonicalize('ŻÓŁĆ@EXAMPLE.COM');
        self::assertSame('żółć@example.com', $result);
    }

    public function testCanonicalizeHandlesSpecialCharacters(): void
    {
        $result = $this->canonicalizer->canonicalize('TEST+123@EXAMPLE.COM');
        self::assertSame('test+123@example.com', $result);
    }

    public function testCanonicalizeHandlesNumbers(): void
    {
        $result = $this->canonicalizer->canonicalize('USER123@EXAMPLE.COM');
        self::assertSame('user123@example.com', $result);
    }

    public function testCanonicalizeWithNonAsciiCharacters(): void
    {
        $result = $this->canonicalizer->canonicalize('JÓZEF@EXAMPLE.COM');
        self::assertSame('józef@example.com', $result);
    }

    public function testCanonicalizeWithCyrillicCharacters(): void
    {
        $result = $this->canonicalizer->canonicalize('ПОЛЬЗОВАТЕЛЬ@EXAMPLE.COM');
        self::assertSame('пользователь@example.com', $result);
    }
}
