<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory;

use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Core\Factory\BuyerFactory;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

final class BuyerFactoryTest extends TestCase
{
    private BuyerFactory $buyerFactory;

    /** @var FactoryInterface&MockObject */
    private FactoryInterface $decoratedFactory;

    /** @var BuyerInterface&MockObject */
    private BuyerInterface $buyer;

    /** @var ContractorInterface&MockObject */
    private ContractorInterface $contractor;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->buyer = $this->createMock(BuyerInterface::class);
        $this->contractor = $this->createMock(ContractorInterface::class);

        $this->decoratedFactory->expects($this->any())
            ->method('createNew')
            ->willReturn($this->buyer);

        $this->buyerFactory = new BuyerFactory($this->decoratedFactory);
    }

    public function testCreateNewThrowsException(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionMessage('createNew');

        $this->buyerFactory->createNew();
    }

    public function testCreateFromContractor(): void
    {
        $companyName = 'Test Company';
        $taxNumber = '123456789';
        $street = 'Test Street';
        $city = 'Test City';
        $postcode = '12-345';

        $this->contractor->method('getCompanyName')->willReturn($companyName);
        $this->contractor->method('getTaxNumber')->willReturn($taxNumber);
        $this->contractor->method('getStreet')->willReturn($street);
        $this->contractor->method('getCity')->willReturn($city);
        $this->contractor->method('getPostcode')->willReturn($postcode);

        $this->decoratedFactory->expects($this->once())->method('createNew');
        $this->buyer->expects($this->once())->method('setCompany')->with($companyName);
        $this->buyer->expects($this->once())->method('setTaxNumber')->with($taxNumber);
        $this->buyer->expects($this->once())->method('setStreet')->with($street);
        $this->buyer->expects($this->once())->method('setCity')->with($city);
        $this->buyer->expects($this->once())->method('setPostCode')->with($postcode);

        $result = $this->buyerFactory->createFromContractor($this->contractor);

        $this->assertSame($this->buyer, $result);
    }

    public function testCreateFromContractorWithNullValues(): void
    {
        $this->contractor->method('getCompanyName')->willReturn(null);
        $this->contractor->method('getTaxNumber')->willReturn(null);
        $this->contractor->method('getStreet')->willReturn(null);
        $this->contractor->method('getCity')->willReturn(null);
        $this->contractor->method('getPostcode')->willReturn(null);

        $this->decoratedFactory->expects($this->once())->method('createNew');
        $this->buyer->expects($this->once())->method('setCompany')->with(null);
        $this->buyer->expects($this->once())->method('setTaxNumber')->with(null);
        $this->buyer->expects($this->once())->method('setStreet')->with(null);
        $this->buyer->expects($this->once())->method('setCity')->with(null);
        $this->buyer->expects($this->once())->method('setPostCode')->with(null);

        $result = $this->buyerFactory->createFromContractor($this->contractor);

        $this->assertSame($this->buyer, $result);
    }
}