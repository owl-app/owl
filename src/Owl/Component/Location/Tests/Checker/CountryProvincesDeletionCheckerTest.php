<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Owl\Component\Location\Checker\CountryProvincesDeletionChecker;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\Province;
use Owl\Component\Location\Model\ZoneMemberInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class CountryProvincesDeletionCheckerTest extends TestCase
{
    private CountryProvincesDeletionChecker $checker;

    /** @var RepositoryInterface<ZoneMemberInterface>&MockObject */
    private $zoneMemberRepository;

    /** @var RepositoryInterface<Province>&MockObject */
    private $provinceRepository;

    protected function setUp(): void
    {
        $this->zoneMemberRepository = $this->createMock(RepositoryInterface::class);
        $this->provinceRepository = $this->createMock(RepositoryInterface::class);

        $this->checker = new CountryProvincesDeletionChecker(
            $this->zoneMemberRepository,
            $this->provinceRepository,
        );
    }

    public function testCountryIsDeletableWhenNoProvincesExist(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        $country->method('getProvinces')->willReturn(new ArrayCollection());

        $this->provinceRepository
            ->method('findBy')
            ->with(['country' => $country])
            ->willReturn([])
        ;

        $this->zoneMemberRepository
            ->method('findOneBy')
            ->with(['code' => []])
            ->willReturn(null)
        ;

        self::assertTrue($this->checker->isDeletable($country));
    }

    public function testCountryIsNotDeletableWhenProvinceIsUsedInZone(): void
    {
        $province = new Province();
        $province->setCode('PL-MAZ');

        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        $country->method('getProvinces')->willReturn(new ArrayCollection());

        $this->provinceRepository
            ->method('findBy')
            ->with(['country' => $country])
            ->willReturn([$province])
        ;

        /** @var ZoneMemberInterface&MockObject $zoneMember */
        $zoneMember = $this->createMock(ZoneMemberInterface::class);

        $this->zoneMemberRepository
            ->method('findOneBy')
            ->with(['code' => ['PL-MAZ']])
            ->willReturn($zoneMember)
        ;

        self::assertFalse($this->checker->isDeletable($country));
    }

    public function testCountryIsDeletableWhenAllProvincesAreInCountryCollection(): void
    {
        $province = new Province();
        $province->setCode('PL-MAZ');

        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        $country->method('getProvinces')->willReturn(new ArrayCollection([$province]));

        $this->provinceRepository
            ->method('findBy')
            ->with(['country' => $country])
            ->willReturn([$province])
        ;

        $this->zoneMemberRepository
            ->method('findOneBy')
            ->with(['code' => []])
            ->willReturn(null)
        ;

        self::assertTrue($this->checker->isDeletable($country));
    }
}