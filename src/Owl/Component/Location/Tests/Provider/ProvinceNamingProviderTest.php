<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Provider;

use Owl\Component\Location\Model\Province;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Provider\ProvinceNamingProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ProvinceNamingProviderTest extends TestCase
{
    private ProvinceNamingProvider $provider;

    /** @var RepositoryInterface&MockObject */
    private $provinceRepository;

    protected function setUp(): void
    {
        $this->provinceRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new ProvinceNamingProvider($this->provinceRepository);
    }

    public function testReturnsEmptyStringWhenAddressHasNoProvinceCode(): void
    {
        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn(null);

        self::assertSame('', $this->provider->getName($address));
    }

    public function testReturnsProvinceNameForValidProvinceCode(): void
    {
        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn('PL-MAZ');

        $province = new Province();
        $province->setName('Mazowieckie');

        $this->provinceRepository
            ->method('findOneBy')
            ->with(['code' => 'PL-MAZ'])
            ->willReturn($province)
        ;

        self::assertSame('Mazowieckie', $this->provider->getName($address));
    }

    public function testThrowsExceptionWhenProvinceNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Province with code "INVALID" not found.');

        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn('INVALID');

        $this->provinceRepository
            ->method('findOneBy')
            ->with(['code' => 'INVALID'])
            ->willReturn(null)
        ;

        $this->provider->getName($address);
    }

    public function testReturnsEmptyStringWhenAddressHasNoProvinceCodeForAbbreviation(): void
    {
        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn(null);

        self::assertSame('', $this->provider->getAbbreviation($address));
    }

    public function testReturnsProvinceAbbreviationWhenAvailable(): void
    {
        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn('PL-MAZ');

        $province = new Province();
        $province->setName('Mazowieckie');
        $province->setAbbreviation('MAZ');

        $this->provinceRepository
            ->method('findOneBy')
            ->with(['code' => 'PL-MAZ'])
            ->willReturn($province)
        ;

        self::assertSame('MAZ', $this->provider->getAbbreviation($address));
    }

    public function testReturnsProvinceNameWhenAbbreviationNotAvailable(): void
    {
        /** @var ProvinceCodeAwareInterface&MockObject $address */
        $address = $this->createMock(ProvinceCodeAwareInterface::class);
        $address->method('getProvinceCode')->willReturn('PL-MAZ');

        $province = new Province();
        $province->setName('Mazowieckie');

        $this->provinceRepository
            ->method('findOneBy')
            ->with(['code' => 'PL-MAZ'])
            ->willReturn($province)
        ;

        self::assertSame('Mazowieckie', $this->provider->getAbbreviation($address));
    }
}
