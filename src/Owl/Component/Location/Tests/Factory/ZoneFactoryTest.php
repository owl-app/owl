<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Factory;

use Owl\Component\Location\Factory\ZoneFactory;
use Owl\Component\Location\Model\Zone;
use Owl\Component\Location\Model\ZoneMember;
use Owl\Component\Location\Model\ZoneMemberInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Factory\FactoryInterface;

final class ZoneFactoryTest extends TestCase
{
    private ZoneFactory $factory;

    /** @var FactoryInterface<Zone>&MockObject */
    private $zoneFactory;

    /** @var FactoryInterface<ZoneMember>&MockObject */
    private $zoneMemberFactory;

    protected function setUp(): void
    {
        $this->zoneFactory = $this->createMock(FactoryInterface::class);
        $this->zoneMemberFactory = $this->createMock(FactoryInterface::class);

        $this->factory = new ZoneFactory($this->zoneFactory, $this->zoneMemberFactory);
    }

    public function testCreatesZone(): void
    {
        $zone = new Zone();

        $this->zoneFactory
            ->method('createNew')
            ->willReturn($zone)
        ;

        self::assertSame($zone, $this->factory->createNew());
    }

    public function testCreatesTypedZone(): void
    {
        $zone = new Zone();

        $this->zoneFactory
            ->method('createNew')
            ->willReturn($zone)
        ;

        $typedZone = $this->factory->createTyped('country');

        self::assertSame('country', $typedZone->getType());
    }

    public function testCreatesZoneWithMembers(): void
    {
        $zone = new Zone();
        $zoneMember = new ZoneMember();

        $this->zoneFactory
            ->method('createNew')
            ->willReturn($zone)
        ;

        $this->zoneMemberFactory
            ->method('createNew')
            ->willReturn($zoneMember)
        ;

        $zoneWithMembers = $this->factory->createWithMembers(['PL']);

        self::assertCount(1, $zoneWithMembers->getMembers());
        self::assertSame('PL', $zoneWithMembers->getMembers()->first()->getCode());
    }

    public function testCreatesZoneWithMultipleMembers(): void
    {
        $zone = new Zone();

        $this->zoneFactory
            ->method('createNew')
            ->willReturn($zone)
        ;

        $this->zoneMemberFactory
            ->method('createNew')
            ->willReturnCallback(fn () => new ZoneMember())
        ;

        $zoneWithMembers = $this->factory->createWithMembers(['PL', 'DE', 'UK']);

        self::assertCount(3, $zoneWithMembers->getMembers());
        self::assertContainsOnlyInstancesOf(ZoneMemberInterface::class, $zoneWithMembers->getMembers());
    }
}
