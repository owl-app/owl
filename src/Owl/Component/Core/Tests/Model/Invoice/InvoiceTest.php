<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model\Invoice;

use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    private Invoice $invoice;

    protected function setUp(): void
    {
        $this->invoice = new Invoice();
    }

    public function testImplementsInvoiceInterface(): void
    {
        self::assertInstanceOf(InvoiceInterface::class, $this->invoice);
    }

    public function testCompanyIsMutable(): void
    {
        $company = $this->createMock(\Owl\Component\Core\Model\CompanyInterface::class);
        $this->invoice->setCompany($company);
        self::assertSame($company, $this->invoice->getCompany());
    }

    public function testContractorIsMutable(): void
    {
        $contractor = $this->createMock(\Owl\Component\Core\Model\ContractorInterface::class);
        $this->invoice->setContractor($contractor);
        self::assertSame($contractor, $this->invoice->getContractor());
    }

    public function testCurrencyIsMutable(): void
    {
        $currency = $this->createMock(\Sylius\Component\Currency\Model\CurrencyInterface::class);
        $this->invoice->setCurrency($currency);
        self::assertSame($currency, $this->invoice->getCurrency());
    }
}
