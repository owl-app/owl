<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Company;
use Owl\Component\Core\Model\CompanyInterface;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    private Company $company;

    protected function setUp(): void
    {
        $this->company = new Company();
    }

    public function testImplementsCompanyInterface(): void
    {
        self::assertInstanceOf(CompanyInterface::class, $this->company);
    }

    public function testCountryCodeIsMutable(): void
    {
        $this->company->setCountryCode('PL');
        self::assertSame('PL', $this->company->getCountryCode());
    }

    public function testProvinceCodeIsMutable(): void
    {
        $this->company->setProvinceCode('MA');
        self::assertSame('MA', $this->company->getProvinceCode());
    }

    public function testZoneIsMutable(): void
    {
        $zone = $this->createMock(\Owl\Component\Location\Model\ZoneInterface::class);
        $this->company->setZone($zone);
        self::assertSame($zone, $this->company->getZone());
    }

    public function testCurrencyIsMutable(): void
    {
        $currency = $this->createMock(\Sylius\Component\Currency\Model\CurrencyInterface::class);
        $this->company->setCurrency($currency);
        self::assertSame($currency, $this->company->getCurrency());
    }
}
