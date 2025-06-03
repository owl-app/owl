<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\Province;
use Owl\Component\Location\Model\ProvinceInterface;

final class ProvinceTest extends TestCase
{
    private Province $province;

    protected function setUp(): void
    {
        $this->province = new Province();
    }

    public function testImplementsProvinceInterface(): void
    {
        self::assertInstanceOf(ProvinceInterface::class, $this->province);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->province->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->province->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->province->setCode('PL-MAZ');
        self::assertSame('PL-MAZ', $this->province->getCode());
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->province->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->province->setName('Mazowieckie');
        self::assertSame('Mazowieckie', $this->province->getName());
    }

    public function testDoesNotBelongToCountryByDefault(): void
    {
        self::assertNull($this->province->getCountry());
    }

    public function testAllowsToAttachItselfToACountry(): void
    {
        /** @var CountryInterface&MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);
        $this->province->setCountry($countryMock);
        self::assertSame($countryMock, $this->province->getCountry());
    }

    public function testToStringReturnsName(): void
    {
        $this->province->setName('Mazowieckie');
        self::assertSame('Mazowieckie', (string) $this->province);
    }
} 