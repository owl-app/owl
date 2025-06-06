<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Suggestion\Tests\Model;

use Owl\Component\Suggestion\Model\Suggestion;
use Owl\Component\Suggestion\Model\SuggestionInterface;
use PHPUnit\Framework\TestCase;

final class SuggestionTest extends TestCase
{
    private Suggestion $suggestion;

    protected function setUp(): void
    {
        $this->suggestion = new Suggestion();
    }

    public function testImplementsSuggestionInterface(): void
    {
        self::assertInstanceOf(SuggestionInterface::class, $this->suggestion);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->suggestion->getId());
    }

    public function testHasNoTitleByDefault(): void
    {
        self::assertNull($this->suggestion->getTitle());
    }

    public function testItsTitleIsMutable(): void
    {
        $this->suggestion->setTitle('Propozycja');
        self::assertSame('Propozycja', $this->suggestion->getTitle());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        self::assertNull($this->suggestion->getDescription());
    }

    public function testItsDescriptionIsMutable(): void
    {
        $this->suggestion->setDescription('Opis propozycji');
        self::assertSame('Opis propozycji', $this->suggestion->getDescription());
    }

    public function testItInitializesCreatedAtByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->suggestion->getCreatedAt());
    }

    public function testItsCreatedAtIsMutable(): void
    {
        $dateTime = new \DateTime('2020-01-01 12:00:00');
        $this->suggestion->setCreatedAt($dateTime);
        self::assertSame($dateTime, $this->suggestion->getCreatedAt());
    }

    public function testHasNoUpdatedAtByDefault(): void
    {
        self::assertNull($this->suggestion->getUpdatedAt());
    }

    public function testItsUpdatedAtIsMutable(): void
    {
        $dateTime = new \DateTime('2020-01-01 12:00:00');
        $this->suggestion->setUpdatedAt($dateTime);
        self::assertSame($dateTime, $this->suggestion->getUpdatedAt());
    }
} 