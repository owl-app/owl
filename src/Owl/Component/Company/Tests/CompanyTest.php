<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Company\Model;

use Owl\Component\Company\Model\Company;
use Owl\Component\Company\Model\CompanyInterface;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase
{
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = new Company();
    }

    public function testShouldImplementCompanyInterface(): void
    {
        self::assertInstanceOf(CompanyInterface::class, $this->company);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->company->getId());
    }

    public function testShouldHaveNoNameByDefault(): void
    {
        self::assertNull($this->company->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->company->setName('Acme');
        self::assertSame('Acme', $this->company->getName());
    }

    public function testShouldHaveNoTaxNumberByDefault(): void
    {
        self::assertNull($this->company->getTaxNumber());
    }

    public function testTaxNumberShouldBeMutable(): void
    {
        $this->company->setTaxNumber('1234567890');
        self::assertSame('1234567890', $this->company->getTaxNumber());
    }

    public function testShouldHaveNoCityByDefault(): void
    {
        self::assertNull($this->company->getCity());
    }

    public function testCityShouldBeMutable(): void
    {
        $this->company->setCity('Warsaw');
        self::assertSame('Warsaw', $this->company->getCity());
    }

    public function testShouldHaveNoStreetByDefault(): void
    {
        self::assertNull($this->company->getStreet());
    }

    public function testStreetShouldBeMutable(): void
    {
        $this->company->setStreet('Main St');
        self::assertSame('Main St', $this->company->getStreet());
    }

    public function testShouldHaveNoBuildingNumberByDefault(): void
    {
        self::assertNull($this->company->getBuildingNumber());
    }

    public function testBuildingNumberShouldBeMutable(): void
    {
        $this->company->setBuildingNumber('10A');
        self::assertSame('10A', $this->company->getBuildingNumber());
        $this->company->setBuildingNumber(null);
        self::assertNull($this->company->getBuildingNumber());
    }

    public function testShouldHaveNoFlatNumberByDefault(): void
    {
        self::assertNull($this->company->getFlatNumber());
    }

    public function testFlatNumberShouldBeMutable(): void
    {
        $this->company->setFlatNumber('5');
        self::assertSame('5', $this->company->getFlatNumber());
        $this->company->setFlatNumber(null);
        self::assertNull($this->company->getFlatNumber());
    }

    public function testShouldHaveNoPostCodeByDefault(): void
    {
        self::assertNull($this->company->getPostCode());
    }

    public function testPostCodeShouldBeMutable(): void
    {
        $this->company->setPostCode('00-001');
        self::assertSame('00-001', $this->company->getPostCode());
    }

    public function testShouldHaveNoPhoneByDefault(): void
    {
        self::assertNull($this->company->getPhone());
    }

    public function testPhoneShouldBeMutable(): void
    {
        $this->company->setPhone('123-456-789');
        self::assertSame('123-456-789', $this->company->getPhone());
    }

    public function testShouldHaveNoEmailByDefault(): void
    {
        self::assertNull($this->company->getEmail());
    }

    public function testEmailShouldBeMutable(): void
    {
        $this->company->setEmail('test@example.com');
        self::assertSame('test@example.com', $this->company->getEmail());
    }

    public function testShouldHaveNoContactPersonByDefault(): void
    {
        self::assertNull($this->company->getContactPerson());
    }

    public function testContactPersonShouldBeMutable(): void
    {
        $this->company->setContactPerson('John Doe');
        self::assertSame('John Doe', $this->company->getContactPerson());
        $this->company->setContactPerson(null);
        self::assertNull($this->company->getContactPerson());
    }

    public function testShouldHaveNoDescriptionByDefault(): void
    {
        self::assertNull($this->company->getDescription());
    }

    public function testDescriptionShouldBeMutable(): void
    {
        $this->company->setDescription('Test description');
        self::assertSame('Test description', $this->company->getDescription());
        $this->company->setDescription(null);
        self::assertNull($this->company->getDescription());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->company->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->company->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->company->setCreatedAt($date);
        self::assertSame($date, $this->company->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->company->setUpdatedAt($date);
        self::assertSame($date, $this->company->getUpdatedAt());
    }

    public function testToStringReturnsName(): void
    {
        $this->company->setName('Acme');
        self::assertSame('Acme', (string) $this->company);
    }
}
