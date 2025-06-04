<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Tests\Model\Seller;

use Owl\Component\Invoice\Model\Seller\Seller;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use PHPUnit\Framework\TestCase;

class SellerTest extends TestCase
{
    private Seller $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seller = new Seller();
    }

    public function testShouldImplementSellerInterface(): void
    {
        self::assertInstanceOf(SellerInterface::class, $this->seller);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->seller->getId());
    }

    public function testShouldHaveNoCompanyByDefault(): void
    {
        self::assertNull($this->seller->getCompany());
    }

    public function testCompanyShouldBeMutable(): void
    {
        $this->seller->setCompany('ABC Corp');
        self::assertSame('ABC Corp', $this->seller->getCompany());

        $this->seller->setCompany(null);
        self::assertNull($this->seller->getCompany());
    }

    public function testShouldHaveNoTaxNumberByDefault(): void
    {
        self::assertNull($this->seller->getTaxNumber());
    }

    public function testTaxNumberShouldBeMutable(): void
    {
        $this->seller->setTaxNumber('PL1234567890');
        self::assertSame('PL1234567890', $this->seller->getTaxNumber());

        $this->seller->setTaxNumber(null);
        self::assertNull($this->seller->getTaxNumber());
    }

    public function testStreetShouldBeMutable(): void
    {
        $this->seller->setStreet('123 Main St');
        self::assertSame('123 Main St', $this->seller->getStreet());
    }

    public function testCityShouldBeMutable(): void
    {
        $this->seller->setCity('Warsaw');
        self::assertSame('Warsaw', $this->seller->getCity());
    }

    public function testPostcodeShouldBeMutable(): void
    {
        $this->seller->setPostcode('00-001');
        self::assertSame('00-001', $this->seller->getPostcode());
    }

    public function testCountryCodeShouldBeMutable(): void
    {
        $this->seller->setCountryCode('PL');
        self::assertSame('PL', $this->seller->getCountryCode());
    }

    public function testAddressFieldsValidation(): void
    {
        $this->seller->setStreet('123 Main St');
        $this->seller->setCity('Warsaw');
        $this->seller->setPostcode('00-001');
        $this->seller->setCountryCode('PL');

        self::assertSame('123 Main St', $this->seller->getStreet());
        self::assertSame('Warsaw', $this->seller->getCity());
        self::assertSame('00-001', $this->seller->getPostcode());
        self::assertSame('PL', $this->seller->getCountryCode());
    }

    public function testFullAddressRepresentation(): void
    {
        $this->seller->setStreet('123 Main St');
        $this->seller->setCity('Warsaw');
        $this->seller->setPostcode('00-001');
        $this->seller->setCountryCode('PL');

        // Test address components work together
        $fullAddress = sprintf(
            '%s, %s %s, %s',
            $this->seller->getStreet(),
            $this->seller->getPostcode(),
            $this->seller->getCity(),
            $this->seller->getCountryCode(),
        );

        self::assertSame('123 Main St, 00-001 Warsaw, PL', $fullAddress);
    }

    public function testFullSellerInfoRepresentation(): void
    {
        $this->seller->setCompany('ABC Corp');
        $this->seller->setTaxNumber('PL1234567890');
        $this->seller->setStreet('123 Main St');
        $this->seller->setCity('Warsaw');
        $this->seller->setPostcode('00-001');
        $this->seller->setCountryCode('PL');

        // Test all seller info components work together
        $sellerInfo = [
            'company' => $this->seller->getCompany(),
            'taxNumber' => $this->seller->getTaxNumber(),
            'street' => $this->seller->getStreet(),
            'city' => $this->seller->getCity(),
            'postcode' => $this->seller->getPostcode(),
            'countryCode' => $this->seller->getCountryCode(),
        ];

        $expected = [
            'company' => 'ABC Corp',
            'taxNumber' => 'PL1234567890',
            'street' => '123 Main St',
            'city' => 'Warsaw',
            'postcode' => '00-001',
            'countryCode' => 'PL',
        ];

        self::assertEquals($expected, $sellerInfo);
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->seller->setCreatedAt($date);
        self::assertSame($date, $this->seller->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->seller->setUpdatedAt($date);
        self::assertSame($date, $this->seller->getUpdatedAt());
    }
}
