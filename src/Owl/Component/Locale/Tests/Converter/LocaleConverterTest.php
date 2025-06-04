<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Tests\Converter;

use Owl\Component\Locale\Converter\LocaleConverter;
use PHPUnit\Framework\TestCase;

class LocaleConverterTest extends TestCase
{
    private LocaleConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new LocaleConverter();
    }

    public function testConvertNameToCode(): void
    {
        $this->assertEquals('en', $this->converter->convertNameToCode('English'));
        $this->assertEquals('pl', $this->converter->convertNameToCode('polski', 'pl'));
        $this->assertEquals('fr', $this->converter->convertNameToCode('French'));
    }

    public function testConvertNameToCodeWithInvalidName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->converter->convertNameToCode('NonExistentLanguage');
    }

    public function testConvertCodeToName(): void
    {
        $this->assertEquals('English', $this->converter->convertCodeToName('en'));
        $this->assertEquals('polski', $this->converter->convertCodeToName('pl', 'pl'));
        $this->assertEquals('French', $this->converter->convertCodeToName('fr'));
    }

    public function testConvertCodeToNameWithInvalidCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->converter->convertCodeToName('xx');
    }
}
