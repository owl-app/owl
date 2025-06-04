<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\ExchangeRate;
use PHPUnit\Framework\TestCase;

final class ExchangeRateTest extends TestCase
{
    private ExchangeRate $exchangeRate;

    protected function setUp(): void
    {
        $this->exchangeRate = new ExchangeRate();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(ExchangeRate::class, $this->exchangeRate);
    }
}
