<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Provider;

use Owl\Component\Invoice\Enum\InvoiceTypeEnum;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Provider\DefaultInvoiceSerieProvider;
use Owl\Component\Invoice\Provider\InvoiceSerieProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class DefaultInvoiceSerieProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $serieRepository;

    private InvoiceSerieProviderInterface $provider;

    protected function setUp(): void
    {
        $this->serieRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new DefaultInvoiceSerieProvider($this->serieRepository);
    }

    public function testGetSerieReturnsDefaultSerieWhenExists(): void
    {
        $type = InvoiceTypeEnum::TYPE_SALES;
        $defaultSerie = $this->createMock(InvoiceSerieInterface::class);

        $this->serieRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => $type])
            ->willReturn($defaultSerie);

        $this->serieRepository
            ->expects($this->never())
            ->method('findBy');

        $result = $this->provider->getSerie($type);
        $this->assertSame($defaultSerie, $result);
    }

    public function testGetSerieReturnsFirstSerieWhenNoDefaultExists(): void
    {
        $type = InvoiceTypeEnum::TYPE_SALES;
        $firstSerie = $this->createMock(InvoiceSerieInterface::class);

        $this->serieRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => $type])
            ->willReturn(null);

        $this->serieRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['invoiceType' => $type], ['id' => 'ASC'], 1)
            ->willReturn([$firstSerie]);

        $result = $this->provider->getSerie($type);
        $this->assertSame($firstSerie, $result);
    }

    public function testGetSerieThrowsExceptionWhenNoSeriesFound(): void
    {
        $type = InvoiceTypeEnum::TYPE_SALES;

        $this->serieRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => $type])
            ->willReturn(null);

        $this->serieRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['invoiceType' => $type], ['id' => 'ASC'], 1)
            ->willReturn([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No invoice serie found');

        $this->provider->getSerie($type);
    }
}
