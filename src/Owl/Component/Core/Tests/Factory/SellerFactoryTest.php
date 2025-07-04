<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory;

use Owl\Component\Core\Factory\SellerFactory;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

final class SellerFactoryTest extends TestCase
{
    private SellerFactory $sellerFactory;

    /** @var FactoryInterface&MockObject */
    private FactoryInterface $decoratedFactory;

    /** @var SellerInterface&MockObject */
    private SellerInterface $seller;

    /** @var CompanyInterface&MockObject */
    private CompanyInterface $company;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->seller = $this->createMock(SellerInterface::class);
        $this->company = $this->createMock(CompanyInterface::class);

        $this->decoratedFactory->expects($this->any())
            ->method('createNew')
            ->willReturn($this->seller);

        $this->sellerFactory = new SellerFactory($this->decoratedFactory);
    }

    public function testCreateNewThrowsException(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionMessage('createNew');

        $this->sellerFactory->createNew();
    }

    public function testCreateFromCompany(): void
    {
        // Setup company mock
        $companyName = 'Test Company';
        $taxNumber = '123456789';
        $street = 'Test Street';
        $city = 'Test City';
        $postcode = '12-345';

        $this->company->method('getName')->willReturn($companyName);
        $this->company->method('getTaxNumber')->willReturn($taxNumber);
        $this->company->method('getStreet')->willReturn($street);
        $this->company->method('getCity')->willReturn($city);
        $this->company->method('getPostcode')->willReturn($postcode);

        // Setup expectations
        $this->decoratedFactory->expects($this->once())->method('createNew');
        $this->seller->expects($this->once())->method('setCompany')->with($companyName);
        $this->seller->expects($this->once())->method('setTaxNumber')->with($taxNumber);
        $this->seller->expects($this->once())->method('setStreet')->with($street);
        $this->seller->expects($this->once())->method('setCity')->with($city);
        $this->seller->expects($this->once())->method('setPostCode')->with($postcode);

        $result = $this->sellerFactory->createFromCompany($this->company);

        $this->assertSame($this->seller, $result);
    }

    public function testCreateFromCompanyWithNullValues(): void
    {
        // Setup company mock with null values
        $this->company->method('getName')->willReturn(null);
        $this->company->method('getTaxNumber')->willReturn(null);
        $this->company->method('getStreet')->willReturn(null);
        $this->company->method('getCity')->willReturn(null);
        $this->company->method('getPostcode')->willReturn(null);

        // Setup expectations
        $this->decoratedFactory->expects($this->once())->method('createNew');
        $this->seller->expects($this->once())->method('setCompany')->with(null);
        $this->seller->expects($this->once())->method('setTaxNumber')->with(null);
        $this->seller->expects($this->once())->method('setStreet')->with(null);
        $this->seller->expects($this->once())->method('setCity')->with(null);
        $this->seller->expects($this->once())->method('setPostCode')->with(null);

        $result = $this->sellerFactory->createFromCompany($this->company);

        $this->assertSame($this->seller, $result);
    }
}