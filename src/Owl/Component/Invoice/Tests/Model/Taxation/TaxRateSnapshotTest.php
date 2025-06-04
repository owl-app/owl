<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model\Taxation;

use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshot;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use PHPUnit\Framework\TestCase;

class TaxRateSnapshotTest extends TestCase
{
    private TaxRateSnapshot $taxRateSnapshot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxRateSnapshot = new TaxRateSnapshot();
    }

    public function testShouldImplementTaxRateSnapshotInterface(): void
    {
        self::assertInstanceOf(TaxRateSnapshotInterface::class, $this->taxRateSnapshot);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->taxRateSnapshot->getId());
    }

    public function testShouldHaveNoCodeByDefault(): void
    {
        self::assertNull($this->taxRateSnapshot->getCode());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->taxRateSnapshot->setCode('VAT_23');
        self::assertSame('VAT_23', $this->taxRateSnapshot->getCode());

        $this->taxRateSnapshot->setCode(null);
        self::assertNull($this->taxRateSnapshot->getCode());
    }

    public function testShouldHaveNoNameByDefault(): void
    {
        self::assertNull($this->taxRateSnapshot->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->taxRateSnapshot->setName('VAT 23%');
        self::assertSame('VAT 23%', $this->taxRateSnapshot->getName());

        $this->taxRateSnapshot->setName(null);
        self::assertNull($this->taxRateSnapshot->getName());
    }

    public function testShouldTrackNameChanges(): void
    {
        self::assertFalse($this->taxRateSnapshot->isNameChanged());

        // Set initial name - should mark as changed
        $this->taxRateSnapshot->setName('VAT 23%');
        self::assertTrue($this->taxRateSnapshot->isNameChanged());
    }

    public function testShouldNotMarkNameAsChangedIfSameValueIsSet(): void
    {
        $this->taxRateSnapshot->setName('VAT 23%');
        // Reset the state manually for testing
        $reflection = new \ReflectionObject($this->taxRateSnapshot);
        $property = $reflection->getProperty('isNameChanged');
        $property->setAccessible(true);
        $property->setValue($this->taxRateSnapshot, false);

        // Set the same name again
        $this->taxRateSnapshot->setName('VAT 23%');
        self::assertFalse($this->taxRateSnapshot->isNameChanged());
    }

    public function testShouldHaveZeroAmountByDefault(): void
    {
        self::assertNull($this->taxRateSnapshot->getAmount());
    }

    public function testAmountShouldBeMutable(): void
    {
        $this->taxRateSnapshot->setAmount(0.23);
        self::assertSame(0.23, $this->taxRateSnapshot->getAmount());

        $this->taxRateSnapshot->setAmount(null);
        self::assertNull($this->taxRateSnapshot->getAmount());
    }

    public function testShouldTrackAmountChanges(): void
    {
        self::assertFalse($this->taxRateSnapshot->isAmountChanged());

        // Set initial amount - should mark as changed
        $this->taxRateSnapshot->setAmount(0.23);
        self::assertTrue($this->taxRateSnapshot->isAmountChanged());
    }

    public function testShouldNotMarkAmountAsChangedIfSameValueIsSet(): void
    {
        $this->taxRateSnapshot->setAmount(0.23);
        // Reset the state manually for testing
        $reflection = new \ReflectionObject($this->taxRateSnapshot);
        $property = $reflection->getProperty('isAmountChanged');
        $property->setAccessible(true);
        $property->setValue($this->taxRateSnapshot, false);

        // Set the same amount again
        $this->taxRateSnapshot->setAmount(0.23);
        self::assertFalse($this->taxRateSnapshot->isAmountChanged());
    }

    public function testShouldConvertAmountToPercentage(): void
    {
        $this->taxRateSnapshot->setAmount(0.23);
        self::assertSame(23.0, $this->taxRateSnapshot->getAmountAsPercentage());
    }

    public function testShouldGenerateLabel(): void
    {
        $this->taxRateSnapshot->setName('VAT');
        $this->taxRateSnapshot->setAmount(0.23);
        self::assertSame('VAT (23%)', $this->taxRateSnapshot->getLabel());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->taxRateSnapshot->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->taxRateSnapshot->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->taxRateSnapshot->setCreatedAt($date);
        self::assertSame($date, $this->taxRateSnapshot->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->taxRateSnapshot->setUpdatedAt($date);
        self::assertSame($date, $this->taxRateSnapshot->getUpdatedAt());
    }
}
