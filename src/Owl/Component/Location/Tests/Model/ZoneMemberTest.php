<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Location\Tests\Model;

use PHPUnit\Framework\TestCase;
use Owl\Component\Location\Model\Zone;
use Owl\Component\Location\Model\ZoneMember;
use Owl\Component\Location\Model\ZoneMemberInterface;

final class ZoneMemberTest extends TestCase
{
    private ZoneMember $zoneMember;

    protected function setUp(): void
    {
        $this->zoneMember = new ZoneMember();
    }

    public function testImplementsZoneMemberInterface(): void
    {
        self::assertInstanceOf(ZoneMemberInterface::class, $this->zoneMember);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->zoneMember->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->zoneMember->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->zoneMember->setCode('PL');
        self::assertSame('PL', $this->zoneMember->getCode());
    }

    public function testDoesNotBelongToZoneByDefault(): void
    {
        self::assertNull($this->zoneMember->getBelongsTo());
    }

    public function testAllowsToAttachItselfToZone(): void
    {
        $zone = new Zone();
        $zone->setCode('EU');
        $zone->setName('European Union');

        $this->zoneMember->setBelongsTo($zone);
        self::assertSame($zone, $this->zoneMember->getBelongsTo());
    }
} 