<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Assigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Invoice\Assigner\ExchangeRateSnapshotAssigner;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;

class ExchangeRateSnapshotAssignerTest extends TestCase
{
    private ExchangeRateSnapshotAssigner $exchangeRateSnapshotAssigner;

    private InvoiceInterface&MockObject $invoice;

    private ExchangeRateCurrencyResolverInterface&MockObject $exchangeRateCurrencyResolver;

    private RepositoryInterface&MockObject $exchangeRateSnapshotRepository;

    protected function setUp(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->exchangeRateCurrencyResolver = $this->createMock(ExchangeRateCurrencyResolverInterface::class);
        $this->exchangeRateSnapshotRepository = $this->createMock(RepositoryInterface::class);

        $this->exchangeRateSnapshotAssigner = new ExchangeRateSnapshotAssigner(
            $this->exchangeRateCurrencyResolver,
            $this->exchangeRateSnapshotRepository
        );
    }

    public function testAssignDoesNothingWhenNoExchangeRateSnapshot(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);

        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn(null);

        $this->invoice->expects($this->never())->method('setExchangeRateSnapshot');
        $this->exchangeRateSnapshotRepository->expects($this->never())->method('findOneBy');

        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignDoesNothingWhenCurrencyCodeMatchesAndRatioNotChanged(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $currency->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->once())
            ->method('isRatioChanged')
            ->willReturn(false);

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);

        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->invoice->expects($this->never())->method('setExchangeRateSnapshot');
        $this->exchangeRateSnapshotRepository->expects($this->never())->method('findOneBy');

        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignUpdatesExchangeRateSnapshotWhenCurrencyCodeDiffers(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);
        $existingSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $currency->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('EUR');

        $exchangeRateSnapshot->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->never())
            ->method('isRatioChanged');

        $exchangeRateSnapshot->expects($this->once())
            ->method('getRatio')
            ->willReturn(1.1);

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);

        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'code' => 'EUR',
                'ratio' => 1.1,
            ])
            ->willReturn($existingSnapshot);

        $this->invoice->expects($this->once())
            ->method('setExchangeRateSnapshot')
            ->with($existingSnapshot);

        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignUpdatesExchangeRateSnapshotWhenRatioChanged(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $currency->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->once())
            ->method('isRatioChanged')
            ->willReturn(true);

        $exchangeRateSnapshot->expects($this->once())
            ->method('getRatio')
            ->willReturn(1.2);

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);

        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'code' => 'USD',
                'ratio' => 1.2,
            ])
            ->willReturn(null);

        $exchangeRateSnapshot->expects($this->once())
            ->method('setCode')
            ->with('USD');

        $this->invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');

        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignSetsCodeWhenNoExistingSnapshotFound(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);
    
        $currency->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('EUR');
    
        $exchangeRateSnapshot->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $exchangeRateSnapshot->expects($this->never())
            ->method('isRatioChanged');
    
        $exchangeRateSnapshot->expects($this->once())
            ->method('getRatio')
            ->willReturn(1.1);
    
        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn($currency);
    
        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'code' => 'EUR',
                'ratio' => 1.1,
            ])
            ->willReturn(null);

        $exchangeRateSnapshot->expects($this->once())
            ->method('setCode')
            ->with('EUR');

        $this->invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');
    
        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesNullCurrency(): void
    {
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $exchangeRateSnapshot->expects($this->never())
            ->method('getCode');

        $exchangeRateSnapshot->expects($this->never())
            ->method('isRatioChanged');

        $exchangeRateSnapshot->expects($this->never())
            ->method('getRatio');

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($this->invoice)
            ->willReturn(null);

        $this->invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->never())
            ->method('findOneBy');

        $exchangeRateSnapshot->expects($this->never())
            ->method('setCode');

        $this->invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');

        $this->exchangeRateSnapshotAssigner->assign($this->invoice);
    }
}