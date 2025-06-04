<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        $this->currency = new Currency();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(Currency::class, $this->currency);
    }
}
