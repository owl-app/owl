<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Contractor\Model;

use Owl\Component\Contractor\Model\Contractor;
use Owl\Component\Contractor\Model\ContractorInterface;
use PHPUnit\Framework\TestCase;

class ContractorTest extends TestCase
{
    private Contractor $contractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contractor = new Contractor();
    }

    public function testShouldImplementContractorInterface(): void
    {
        self::assertInstanceOf(ContractorInterface::class, $this->contractor);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->contractor->getId());
    }

    public function testShouldHaveNoCompanyNameByDefault(): void
    {
        self::assertNull($this->contractor->getCompanyName());
    }

    public function testCompanyNameShouldBeMutable(): void
    {
        $this->contractor->setCompanyName('Acme Inc');
        self::assertSame('Acme Inc', $this->contractor->getCompanyName());
        $this->contractor->setCompanyName(null);
        self::assertNull($this->contractor->getCompanyName());
    }

    public function testShouldHaveNoTaxNumberByDefault(): void
    {
        self::assertNull($this->contractor->getTaxNumber());
    }

    public function testTaxNumberShouldBeMutable(): void
    {
        $this->contractor->setTaxNumber('1234567890');
        self::assertSame('1234567890', $this->contractor->getTaxNumber());
        $this->contractor->setTaxNumber(null);
        self::assertNull($this->contractor->getTaxNumber());
    }

    public function testShouldHaveNoStreetByDefault(): void
    {
        self::assertNull($this->contractor->getStreet());
    }

    public function testStreetShouldBeMutable(): void
    {
        $this->contractor->setStreet('Main St');
        self::assertSame('Main St', $this->contractor->getStreet());
    }

    public function testShouldHaveNoBuildingNumberByDefault(): void
    {
        self::assertNull($this->contractor->getBuildingNumber());
    }

    public function testBuildingNumberShouldBeMutable(): void
    {
        $this->contractor->setBuildingNumber('10A');
        self::assertSame('10A', $this->contractor->getBuildingNumber());
        $this->contractor->setBuildingNumber(null);
        self::assertNull($this->contractor->getBuildingNumber());
    }

    public function testShouldHaveNoFlatNumberByDefault(): void
    {
        self::assertNull($this->contractor->getFlatNumber());
    }

    public function testFlatNumberShouldBeMutable(): void
    {
        $this->contractor->setFlatNumber('5');
        self::assertSame('5', $this->contractor->getFlatNumber());
        $this->contractor->setFlatNumber(null);
        self::assertNull($this->contractor->getFlatNumber());
    }

    public function testShouldHaveNoCityByDefault(): void
    {
        self::assertNull($this->contractor->getCity());
    }

    public function testCityShouldBeMutable(): void
    {
        $this->contractor->setCity('Warsaw');
        self::assertSame('Warsaw', $this->contractor->getCity());
    }

    public function testShouldHaveNoPostCodeByDefault(): void
    {
        self::assertNull($this->contractor->getPostCode());
    }

    public function testPostCodeShouldBeMutable(): void
    {
        $this->contractor->setPostCode('00-001');
        self::assertSame('00-001', $this->contractor->getPostCode());
    }

    public function testShouldHaveNoEmailByDefault(): void
    {
        self::assertNull($this->contractor->getEmail());
    }

    public function testEmailShouldBeMutable(): void
    {
        $this->contractor->setEmail('test@example.com');
        self::assertSame('test@example.com', $this->contractor->getEmail());
        $this->contractor->setEmail(null);
        self::assertNull($this->contractor->getEmail());
    }

    public function testShouldHaveNoCountryCodeByDefault(): void
    {
        $reflection = new \ReflectionClass($this->contractor);
        $property = $reflection->getProperty('countryCode');
        $property->setAccessible(true);
        self::assertNull($property->getValue($this->contractor));
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->contractor->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->contractor->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->contractor->setCreatedAt($date);
        self::assertSame($date, $this->contractor->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->contractor->setUpdatedAt($date);
        self::assertSame($date, $this->contractor->getUpdatedAt());
    }
}
