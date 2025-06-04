<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Contractor;
use Owl\Component\Core\Model\ContractorInterface;
use PHPUnit\Framework\TestCase;

final class ContractorTest extends TestCase
{
    private Contractor $contractor;

    protected function setUp(): void
    {
        $this->contractor = new Contractor();
    }

    public function testImplementsContractorInterface(): void
    {
        self::assertInstanceOf(ContractorInterface::class, $this->contractor);
    }

    public function testCurrencyIsMutable(): void
    {
        $currency = $this->createMock(\Sylius\Component\Currency\Model\CurrencyInterface::class);
        $this->contractor->setCurrency($currency);
        self::assertSame($currency, $this->contractor->getCurrency());
    }
}
