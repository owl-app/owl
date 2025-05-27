<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Generator;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Owl\Component\Invoice\Generator\InvoiceNumberGenerator;

class InvoiceNumberGeneratorTest extends TestCase
{
    private InvoiceNumberGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new InvoiceNumberGenerator();
    }

    #[DataProvider('formatProvider')]
    public function testGenerate(string $format, int $number, \DateTimeInterface $date, string $expected): void
    {
        $result = $this->generator->generate($format, $number, $date);
        $this->assertSame($expected, $result);
    }

    public static function formatProvider(): iterable
    {
        $date1 = new \DateTimeImmutable('2023-05-15');
        $date2 = new \DateTimeImmutable('2024-01-03');
        $date3 = new \DateTimeImmutable('2022-12-31');

        yield 'Simple format' => [
            'INV/__NUMBER__', 
            123, 
            $date1, 
            'INV/123'
        ];

        yield 'Format with year' => [
            'INV/__YYYY__/__NUMBER__', 
            456, 
            $date1, 
            'INV/2023/456'
        ];

        yield 'Format with month' => [
            'INV/__MM__/__NUMBER__', 
            789, 
            $date1, 
            'INV/05/789'
        ];

        yield 'Format with year and month' => [
            'INV/__YYYY__/__MM__/__NUMBER__', 
            101, 
            $date1, 
            'INV/2023/05/101'
        ];
        
        yield 'Format with single-digit month' => [
            '__YYYY__/__MM__/__NUMBER__', 
            1, 
            $date2, 
            '2024/01/1'
        ];

        yield 'Format with year and month at different positions' => [
            '__NUMBER__/__MM__/__YYYY__', 
            9999, 
            $date3, 
            '9999/12/2022'
        ];

        yield 'Format with multiple placeholders' => [
            'INV-__YYYY__-__MM__-__NUMBER__-__YYYY__', 
            42, 
            $date1, 
            'INV-2023-05-42-2023'
        ];

        yield 'Format without placeholders' => [
            'INVOICE', 
            42, 
            $date1, 
            'INVOICE'
        ];
    }
}