<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model\Currency;

use PHPUnit\Framework\TestCase;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshot;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;

class ExchangeRateSnapshotTest extends TestCase
{
    private ExchangeRateSnapshot $exchangeRateSnapshot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exchangeRateSnapshot = new ExchangeRateSnapshot();
    }

    public function testShouldImplementExchangeRateSnapshotInterface(): void
    {
        self::assertInstanceOf(ExchangeRateSnapshotInterface::class, $this->exchangeRateSnapshot);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->exchangeRateSnapshot->getId());
    }

    public function testShouldHaveNoCodeByDefault(): void
    {
        self::assertNull($this->exchangeRateSnapshot->getCode());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->exchangeRateSnapshot->setCode('USD');
        self::assertSame('USD', $this->exchangeRateSnapshot->getCode());

        $this->exchangeRateSnapshot->setCode(null);
        self::assertNull($this->exchangeRateSnapshot->getCode());
    }

    public function testShouldHaveNoRatioByDefault(): void
    {
        self::assertNull($this->exchangeRateSnapshot->getRatio());
    }

    public function testRatioShouldBeMutable(): void
    {
        $this->exchangeRateSnapshot->setRatio(4.25);
        self::assertSame(4.25, $this->exchangeRateSnapshot->getRatio());

        $this->exchangeRateSnapshot->setRatio(null);
        self::assertNull($this->exchangeRateSnapshot->getRatio());
    }

    public function testRatioChangedShouldBeFalseByDefault(): void
    {
        self::assertFalse($this->exchangeRateSnapshot->isRatioChanged());
    }

    public function testChangingRatioShouldSetRatioChangedFlag(): void
    {
        $this->exchangeRateSnapshot->setRatio(4.25);
        self::assertTrue($this->exchangeRateSnapshot->isRatioChanged());
    }

    public function testSettingSameRatioShouldNotSetRatioChangedFlag(): void
    {
        // Initial set
        $this->exchangeRateSnapshot->setRatio(4.25);

        // Reset the flag for testing
        $reflection = new \ReflectionProperty($this->exchangeRateSnapshot, 'isRatioChanged');
        $reflection->setAccessible(true);
        $reflection->setValue($this->exchangeRateSnapshot, false);

        // Set the same value again
        $this->exchangeRateSnapshot->setRatio(4.25);
        self::assertFalse($this->exchangeRateSnapshot->isRatioChanged());
    }

    public function testRatioGetterShouldConvertStringToFloat(): void
    {
        // Simulate Doctrine hydrating a decimal as string
        $reflection = new \ReflectionProperty($this->exchangeRateSnapshot, 'ratio');
        $reflection->setAccessible(true);
        $reflection->setValue($this->exchangeRateSnapshot, '4.25');

        self::assertSame(4.25, $this->exchangeRateSnapshot->getRatio());
        self::assertIsFloat($this->exchangeRateSnapshot->getRatio());
    }

    public function testRatioGetterShouldReturnNullForNullValue(): void
    {
        $reflection = new \ReflectionProperty($this->exchangeRateSnapshot, 'ratio');
        $reflection->setAccessible(true);
        $reflection->setValue($this->exchangeRateSnapshot, null);

        self::assertNull($this->exchangeRateSnapshot->getRatio());
    }

    public function testRatioGetterShouldPreserveFloatType(): void
    {
        $reflection = new \ReflectionProperty($this->exchangeRateSnapshot, 'ratio');
        $reflection->setAccessible(true);
        $reflection->setValue($this->exchangeRateSnapshot, 4.25);

        self::assertSame(4.25, $this->exchangeRateSnapshot->getRatio());
        self::assertIsFloat($this->exchangeRateSnapshot->getRatio());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->exchangeRateSnapshot->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->exchangeRateSnapshot->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->exchangeRateSnapshot->setCreatedAt($date);
        self::assertSame($date, $this->exchangeRateSnapshot->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->exchangeRateSnapshot->setUpdatedAt($date);
        self::assertSame($date, $this->exchangeRateSnapshot->getUpdatedAt());
    }
}
