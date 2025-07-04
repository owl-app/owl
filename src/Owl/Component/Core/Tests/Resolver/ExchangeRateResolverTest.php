<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Resolver;

use Owl\Component\Core\Resolver\ExchangeRateResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\ExchangeRate;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateResolverTest extends TestCase
{
    private ExchangeRateRepositoryInterface&MockObject $exchangeRateRepository;

    private ExchangeRateResolver $exchangeRateResolver;

    protected function setUp(): void
    {
        $this->exchangeRateRepository = $this->getMockBuilder(ExchangeRateRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exchangeRateResolver = new ExchangeRateResolver($this->exchangeRateRepository);
    }

    public function testGetRatioWhenExchangeRateExists(): void
    {
        $sourceCurrency = new Currency();
        $sourceCurrency->setCode('USD');

        $targetCurrency = new Currency();
        $targetCurrency->setCode('EUR');

        $exchangeRate = new ExchangeRate();
        $exchangeRate->setSourceCurrency($sourceCurrency);
        $exchangeRate->setTargetCurrency($targetCurrency);
        $exchangeRate->setRatio(0.85);

        $this->exchangeRateRepository
            ->expects($this->once())
            ->method('findOneWithCurrencyPair')
            ->with('EUR', 'USD')
            ->willReturn($exchangeRate);

        $ratio = $this->exchangeRateResolver->getRatio('EUR', 'USD');

        $this->assertEquals(0.85, $ratio);
    }

    public function testGetRatioWhenExchangeRateExistsWithReversedCurrencies(): void
    {
        $sourceCurrency = new Currency();
        $sourceCurrency->setCode('EUR');

        $targetCurrency = new Currency();
        $targetCurrency->setCode('USD');

        $exchangeRate = new ExchangeRate();
        $exchangeRate->setSourceCurrency($sourceCurrency);
        $exchangeRate->setTargetCurrency($targetCurrency);
        $exchangeRate->setRatio(1.18);

        $this->exchangeRateRepository
            ->expects($this->once())
            ->method('findOneWithCurrencyPair')
            ->with('EUR', 'USD')
            ->willReturn($exchangeRate);

        $ratio = $this->exchangeRateResolver->getRatio('EUR', 'USD');

        $this->assertEquals(1 / 1.18, $ratio);
    }

    public function testGetRatioWhenExchangeRateDoesNotExist(): void
    {
        $this->exchangeRateRepository
            ->expects($this->once())
            ->method('findOneWithCurrencyPair')
            ->with('PLN', 'GBP')
            ->willReturn(null);

        $ratio = $this->exchangeRateResolver->getRatio('PLN', 'GBP');

        $this->assertEquals(0, $ratio);
    }
}
