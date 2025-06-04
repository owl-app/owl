<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Converter;

use Owl\Component\Location\Converter\CountryNameConverter;
use Owl\Component\Location\Converter\CountryNameConverterInterface;
use PHPUnit\Framework\TestCase;

final class CountryNameConverterTest extends TestCase
{
    private CountryNameConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new CountryNameConverter();
    }

    public function testImplementsCountryNameConverterInterface(): void
    {
        self::assertInstanceOf(CountryNameConverterInterface::class, $this->converter);
    }

    public function testConvertsCountryNameToCodeForDefaultLocale(): void
    {
        self::assertSame('PL', $this->converter->convertToCode('Poland'));
        self::assertSame('US', $this->converter->convertToCode('United States'));
        self::assertSame('GB', $this->converter->convertToCode('United Kingdom'));
    }

    public function testConvertsCountryNameToCodeForPolishLocale(): void
    {
        self::assertSame('PL', $this->converter->convertToCode('Polska', 'pl'));
        self::assertSame('US', $this->converter->convertToCode('Stany Zjednoczone', 'pl'));
        self::assertSame('GB', $this->converter->convertToCode('Wielka Brytania', 'pl'));
    }

    public function testConvertsCountryNameToCodeForGermanLocale(): void
    {
        self::assertSame('PL', $this->converter->convertToCode('Polen', 'de'));
        self::assertSame('US', $this->converter->convertToCode('Vereinigte Staaten', 'de'));
        self::assertSame('GB', $this->converter->convertToCode('Vereinigtes Königreich', 'de'));
    }

    public function testThrowsExceptionForInvalidCountryName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Country "Invalid Country" not found!');

        $this->converter->convertToCode('Invalid Country');
    }

    public function testThrowsExceptionForInvalidCountryNameInDifferentLocale(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Country "Invalid Country" not found!');

        $this->converter->convertToCode('Invalid Country', 'pl');
    }
}
