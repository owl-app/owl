<?php

declare(strict_types=1);

namespace Owl\Component\Location\Tests\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Owl\Component\Location\Model\Zone;
use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Model\ZoneMember;

final class ZoneTest extends TestCase
{
    private Zone $zone;

    protected function setUp(): void
    {
        $this->zone = new Zone();
    }

    public function testImplementsZoneInterface(): void
    {
        self::assertInstanceOf(ZoneInterface::class, $this->zone);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->zone->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->zone->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->zone->setCode('EU');
        self::assertSame('EU', $this->zone->getCode());
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->zone->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->zone->setName('European Union');
        self::assertSame('European Union', $this->zone->getName());
    }

    public function testHasNoTypeByDefault(): void
    {
        self::assertNull($this->zone->getType());
    }

    public function testItsTypeIsMutable(): void
    {
        $this->zone->setType('country');
        self::assertSame('country', $this->zone->getType());
    }

    public function testInitializesMembersCollection(): void
    {
        self::assertInstanceOf(Collection::class, $this->zone->getMembers());
        self::assertTrue($this->zone->getMembers()->isEmpty());
    }

    public function testAddsMember(): void
    {
        $member = new ZoneMember();
        $member->setCode('PL');

        $this->zone->addMember($member);
        
        self::assertTrue($this->zone->hasMember($member));
        self::assertCount(1, $this->zone->getMembers());
        self::assertSame($this->zone, $member->getBelongsTo());
    }

    public function testRemovesMember(): void
    {
        $member = new ZoneMember();
        $member->setCode('PL');
        
        $this->zone->addMember($member);
        $this->zone->removeMember($member);
        
        self::assertFalse($this->zone->hasMember($member));
        self::assertCount(0, $this->zone->getMembers());
        self::assertNull($member->getBelongsTo());
    }

    public function testToStringReturnsName(): void
    {
        $this->zone->setName('European Union');
        self::assertSame('European Union', (string) $this->zone);
    }
} 