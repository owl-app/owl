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

    private ExchangeRateCurrencyResolverInterface|MockObject $exchangeRateCurrencyResolver;

    private RepositoryInterface|MockObject $exchangeRateSnapshotRepository;

    protected function setUp(): void
    {
        $this->exchangeRateCurrencyResolver = $this->createMock(ExchangeRateCurrencyResolverInterface::class);
        $this->exchangeRateSnapshotRepository = $this->createMock(RepositoryInterface::class);

        $this->exchangeRateSnapshotAssigner = new ExchangeRateSnapshotAssigner(
            $this->exchangeRateCurrencyResolver,
            $this->exchangeRateSnapshotRepository
        );
    }

    public function testAssignDoesNothingWhenNoExchangeRateSnapshot(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($invoice)
            ->willReturn($currency);

        $invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn(null);

        $invoice->expects($this->never())->method('setExchangeRateSnapshot');
        $this->exchangeRateSnapshotRepository->expects($this->never())->method('findOneBy');

        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }

    public function testAssignDoesNothingWhenCurrencyCodeMatchesAndRatioNotChanged(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
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
            ->with($invoice)
            ->willReturn($currency);

        $invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $invoice->expects($this->never())->method('setExchangeRateSnapshot');
        $this->exchangeRateSnapshotRepository->expects($this->never())->method('findOneBy');

        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }

    public function testAssignUpdatesExchangeRateSnapshotWhenCurrencyCodeDiffers(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
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
            ->with($invoice)
            ->willReturn($currency);

        $invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'code' => 'EUR',
                'ratio' => 1.1,
            ])
            ->willReturn($existingSnapshot);

        $invoice->expects($this->once())
            ->method('setExchangeRateSnapshot')
            ->with($existingSnapshot);

        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }

    public function testAssignUpdatesExchangeRateSnapshotWhenRatioChanged(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
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
            ->with($invoice)
            ->willReturn($currency);

        $invoice->expects($this->once())
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

        $invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');

        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }

    public function testAssignSetsCodeWhenNoExistingSnapshotFound(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
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
            ->with($invoice)
            ->willReturn($currency);
    
        $invoice->expects($this->once())
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

        $invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');
    
        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }

    public function testAssignHandlesNullCurrency(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $exchangeRateSnapshot = $this->createMock(ExchangeRateSnapshotInterface::class);

        $exchangeRateSnapshot->expects($this->never())
            ->method('getCode');

        $exchangeRateSnapshot->expects($this->never())
            ->method('isRatioChanged');

        $exchangeRateSnapshot->expects($this->never())
            ->method('getRatio');

        $this->exchangeRateCurrencyResolver->expects($this->once())
            ->method('resolve')
            ->with($invoice)
            ->willReturn(null);

        $invoice->expects($this->once())
            ->method('getExchangeRateSnapshot')
            ->willReturn($exchangeRateSnapshot);

        $this->exchangeRateSnapshotRepository->expects($this->never())
            ->method('findOneBy');

        $exchangeRateSnapshot->expects($this->never())
            ->method('setCode');

        $invoice->expects($this->never())
            ->method('setExchangeRateSnapshot');

        $this->exchangeRateSnapshotAssigner->assign($invoice);
    }
}