<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Invoice\Currency;

use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolver;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Model\ContractorInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;

class ExchangeRateCurrencyResolverTest extends TestCase
{
    private ExchangeRateCurrencyResolver $resolver;

    private CurrencyInterface&MockObject $invoiceCurrency;

    protected function setUp(): void
    {
        $this->invoiceCurrency = $this->createMock(CurrencyInterface::class);

        $this->resolver = new ExchangeRateCurrencyResolver();
    }

    public function testReturnsCompanyCurrencyIfDifferentFromInvoiceCurrency(): void
    {
        $companyCurrency = $this->createMock(CurrencyInterface::class);
        $company = $this->createMock(CompanyInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->invoiceCurrency->method('getCode')->willReturn('USD');

        $companyCurrency->method('getCode')->willReturn('EUR');
        $company->method('getCurrency')->willReturn($companyCurrency);

        $invoice->method('getCurrency')->willReturn($this->invoiceCurrency);
        $invoice->method('getCompany')->willReturn($company);

        $this->assertSame($companyCurrency, $this->resolver->resolve($invoice));
    }

    public function testReturnsContractorCurrencyIfDifferentFromInvoiceCurrency(): void
    {
        $contractorCurrency = $this->createMock(CurrencyInterface::class);
        $contractor = $this->createMock(ContractorInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->invoiceCurrency->method('getCode')->willReturn('USD');

        $contractorCurrency->method('getCode')->willReturn('PLN');
        $contractor->method('getCurrency')->willReturn($contractorCurrency);

        $invoice->method('getCurrency')->willReturn($this->invoiceCurrency);
        $invoice->method('getCompany')->willReturn(null);
        $invoice->method('getContractor')->willReturn($contractor);

        $this->assertSame($contractorCurrency, $this->resolver->resolve($invoice));
    }

    public function testReturnsNullIfNoCurrencyDifference(): void
    {
        $company = $this->createMock(CompanyInterface::class);
        $contractor = $this->createMock(ContractorInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->invoiceCurrency->method('getCode')->willReturn('USD');

        $company->method('getCurrency')->willReturn($this->invoiceCurrency);

        $contractor->method('getCurrency')->willReturn($this->invoiceCurrency);

        $invoice->method('getCurrency')->willReturn($this->invoiceCurrency);
        $invoice->method('getCompany')->willReturn($company);
        $invoice->method('getContractor')->willReturn($contractor);

        $this->assertNull($this->resolver->resolve($invoice));
    }
}
