<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model\Taxation;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Invoice\Model\Taxation\TaxRate;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Location\Model\ZoneInterface;

class TaxRateTest extends TestCase
{
    private TaxRate $taxRate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxRate = new TaxRate();
    }

    public function testShouldImplementTaxRateInterface(): void
    {
        self::assertInstanceOf(TaxRateInterface::class, $this->taxRate);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->taxRate->getId());
    }

    public function testShouldHaveNoCodeByDefault(): void
    {
        self::assertNull($this->taxRate->getCode());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->taxRate->setCode('VAT_23');
        self::assertSame('VAT_23', $this->taxRate->getCode());

        $this->taxRate->setCode(null);
        self::assertNull($this->taxRate->getCode());
    }

    public function testShouldHaveNoNameByDefault(): void
    {
        self::assertNull($this->taxRate->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->taxRate->setName('VAT 23%');
        self::assertSame('VAT 23%', $this->taxRate->getName());

        $this->taxRate->setName(null);
        self::assertNull($this->taxRate->getName());
    }

    public function testShouldHaveZeroAmountByDefault(): void
    {
        self::assertNull($this->taxRate->getAmount());
    }

    public function testAmountShouldBeMutable(): void
    {
        $this->taxRate->setAmount(0.23);
        self::assertSame(0.23, $this->taxRate->getAmount());

        $this->taxRate->setAmount(null);
        self::assertNull($this->taxRate->getAmount());
    }

    public function testShouldConvertAmountToPercentage(): void
    {
        $this->taxRate->setAmount(0.23);
        self::assertSame(23.0, $this->taxRate->getAmountAsPercentage());
    }

    public function testShouldGenerateLabel(): void
    {
        $this->taxRate->setName('VAT');
        $this->taxRate->setAmount(0.23);
        self::assertSame('VAT (23%)', $this->taxRate->getLabel());
    }

    public function testShouldHaveNoStartDateByDefault(): void
    {
        self::assertNull($this->taxRate->getStartDate());
    }

    public function testStartDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->taxRate->setStartDate($date);
        self::assertSame($date, $this->taxRate->getStartDate());

        $this->taxRate->setStartDate(null);
        self::assertNull($this->taxRate->getStartDate());
    }

    public function testShouldHaveNoEndDateByDefault(): void
    {
        self::assertNull($this->taxRate->getEndDate());
    }

    public function testEndDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-12-31');
        $this->taxRate->setEndDate($date);
        self::assertSame($date, $this->taxRate->getEndDate());

        $this->taxRate->setEndDate(null);
        self::assertNull($this->taxRate->getEndDate());
    }

    public function testShouldInitializeZonesCollectionByDefault(): void
    {
        self::assertInstanceOf(Collection::class, $this->taxRate->getZones());
        self::assertCount(0, $this->taxRate->getZones());
    }

    public function testShouldCheckIfHasZone(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        self::assertFalse($this->taxRate->hasZone($zone));

        $this->taxRate->addZone($zone);
        self::assertTrue($this->taxRate->hasZone($zone));
    }

    public function testShouldAddZone(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $this->taxRate->addZone($zone);
        self::assertCount(1, $this->taxRate->getZones());
        self::assertTrue($this->taxRate->getZones()->contains($zone));
    }

    public function testShouldNotAddSameZoneTwice(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $this->taxRate->addZone($zone);
        $this->taxRate->addZone($zone);
        self::assertCount(1, $this->taxRate->getZones());
    }

    public function testShouldRemoveZone(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $this->taxRate->addZone($zone);
        self::assertCount(1, $this->taxRate->getZones());

        $this->taxRate->removeZone($zone);
        self::assertCount(0, $this->taxRate->getZones());
        self::assertFalse($this->taxRate->hasZone($zone));
    }

    public function testShouldIgnoreRemovingNonExistingZone(): void
    {
        $zone = $this->createMock(ZoneInterface::class);
        $this->taxRate->removeZone($zone);
        self::assertCount(0, $this->taxRate->getZones());
    }

    public function testShouldClearZones(): void
    {
        $zone1 = $this->createMock(ZoneInterface::class);
        $zone2 = $this->createMock(ZoneInterface::class);

        $this->taxRate->addZone($zone1);
        $this->taxRate->addZone($zone2);
        self::assertCount(2, $this->taxRate->getZones());

        $this->taxRate->clearZones();
        self::assertCount(0, $this->taxRate->getZones());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->taxRate->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->taxRate->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->taxRate->setCreatedAt($date);
        self::assertSame($date, $this->taxRate->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->taxRate->setUpdatedAt($date);
        self::assertSame($date, $this->taxRate->getUpdatedAt());
    }
}
