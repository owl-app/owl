<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Owl\Component\Location\Model\Country;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\Province;

final class CountryTest extends TestCase
{
    private Country $country;

    protected function setUp(): void
    {
        $this->country = new Country();
    }

    public function testImplementsCountryInterface(): void
    {
        self::assertInstanceOf(CountryInterface::class, $this->country);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->country->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->country->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->country->setCode('PL');
        self::assertSame('PL', $this->country->getCode());
    }

    public function testHasNoNameWithoutCode(): void
    {
        self::assertNull($this->country->getName());
    }

    public function testReturnsNameForCode(): void
    {
        $this->country->setCode('PL');
        self::assertSame('Poland', $this->country->getName('en'));
    }

    public function testInitializesProvincesCollection(): void
    {
        self::assertInstanceOf(Collection::class, $this->country->getProvinces());
        self::assertTrue($this->country->getProvinces()->isEmpty());
    }

    public function testHasNoProvincesAddedByDefault(): void
    {
        self::assertFalse($this->country->hasProvinces());
    }

    public function testAddsProvince(): void
    {
        $province = new Province();
        $province->setCode('PL-MAZ');
        $province->setName('Mazowieckie');

        $this->country->addProvince($province);
        self::assertTrue($this->country->hasProvince($province));
        self::assertSame($this->country, $province->getCountry());
    }

    public function testRemovesProvince(): void
    {
        $province = new Province();
        $province->setCode('PL-MAZ');

        $this->country->addProvince($province);
        $this->country->removeProvince($province);

        self::assertFalse($this->country->hasProvince($province));
        self::assertNull($province->getCountry());
    }

    public function testIsEnabledByDefault(): void
    {
        self::assertTrue($this->country->isEnabled());
    }

    public function testCanBeDisabled(): void
    {
        $this->country->disable();
        self::assertFalse($this->country->isEnabled());
    }

    public function testCanBeEnabled(): void
    {
        $this->country->disable();
        $this->country->enable();
        self::assertTrue($this->country->isEnabled());
    }

    public function testToStringReturnsNameOrCode(): void
    {
        $this->country->setCode('PL');
        self::assertSame('Poland', (string) $this->country);
    }
} 