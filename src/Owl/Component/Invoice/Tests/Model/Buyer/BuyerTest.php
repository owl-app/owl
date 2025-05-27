<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model\Buyer;

use PHPUnit\Framework\TestCase;
use Owl\Component\Invoice\Model\Buyer\Buyer;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;

class BuyerTest extends TestCase
{
    private Buyer $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = new Buyer();
    }

    public function testShouldImplementBuyerInterface(): void
    {
        self::assertInstanceOf(BuyerInterface::class, $this->buyer);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->buyer->getId());
    }

    public function testShouldHaveNoCompanyByDefault(): void
    {
        self::assertNull($this->buyer->getCompany());
    }

    public function testCompanyShouldBeMutable(): void
    {
        $this->buyer->setCompany('Acme Inc.');
        self::assertSame('Acme Inc.', $this->buyer->getCompany());

        $this->buyer->setCompany(null);
        self::assertNull($this->buyer->getCompany());
    }

    public function testShouldHaveNoTaxNumberByDefault(): void
    {
        self::assertNull($this->buyer->getTaxNumber());
    }

    public function testTaxNumberShouldBeMutable(): void
    {
        $this->buyer->setTaxNumber('PL1234567890');
        self::assertSame('PL1234567890', $this->buyer->getTaxNumber());

        $this->buyer->setTaxNumber(null);
        self::assertNull($this->buyer->getTaxNumber());
    }

    public function testStreetShouldBeMutable(): void
    {
        // Initialize the property first to avoid the "not initialized" error
        $this->buyer->setStreet('123 Main St');
        self::assertSame('123 Main St', $this->buyer->getStreet());
    }

    public function testCityShouldBeMutable(): void
    {
        // Initialize the property first to avoid the "not initialized" error
        $this->buyer->setCity('New York');
        self::assertSame('New York', $this->buyer->getCity());
    }

    public function testPostcodeShouldBeMutable(): void
    {
        // Initialize the property first to avoid the "not initialized" error
        $this->buyer->setPostcode('10001');
        self::assertSame('10001', $this->buyer->getPostcode());
    }

    public function testCountryCodeShouldBeMutable(): void
    {
        // Initialize the property first to avoid the "not initialized" error
        $this->buyer->setCountryCode('US');
        self::assertSame('US', $this->buyer->getCountryCode());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->buyer->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->buyer->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->buyer->setCreatedAt($date);
        self::assertSame($date, $this->buyer->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->buyer->setUpdatedAt($date);
        self::assertSame($date, $this->buyer->getUpdatedAt());
    }

    public function testFullAddressRepresentation(): void
    {
        // Initialize all required properties to avoid errors
        $this->buyer->setStreet('123 Main St');
        $this->buyer->setCity('New York');
        $this->buyer->setPostcode('10001');
        $this->buyer->setCountryCode('US');

        // Test address components work together
        $fullAddress = sprintf(
            '%s, %s %s, %s',
            $this->buyer->getStreet(),
            $this->buyer->getPostcode(),
            $this->buyer->getCity(),
            $this->buyer->getCountryCode()
        );

        self::assertSame('123 Main St, 10001 New York, US', $fullAddress);
    }

    public function testFullBuyerInfoRepresentation(): void
    {
        // Initialize all required properties to avoid errors
        $this->buyer->setCompany('Acme Inc.');
        $this->buyer->setTaxNumber('PL1234567890');
        $this->buyer->setStreet('123 Main St');
        $this->buyer->setCity('New York');
        $this->buyer->setPostcode('10001');
        $this->buyer->setCountryCode('US');

        // Test all buyer info components work together
        $buyerInfo = [
            'company' => $this->buyer->getCompany(),
            'taxNumber' => $this->buyer->getTaxNumber(),
            'street' => $this->buyer->getStreet(),
            'city' => $this->buyer->getCity(),
            'postcode' => $this->buyer->getPostcode(),
            'countryCode' => $this->buyer->getCountryCode(),
        ];

        $expected = [
            'company' => 'Acme Inc.',
            'taxNumber' => 'PL1234567890',
            'street' => '123 Main St',
            'city' => 'New York',
            'postcode' => '10001',
            'countryCode' => 'US',
        ];

        self::assertEquals($expected, $buyerInfo);
    }
}
