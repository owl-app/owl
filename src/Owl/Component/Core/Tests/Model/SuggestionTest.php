<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Suggestion;
use Owl\Component\Core\Model\SuggestionInterface;
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

    public function testUserIsMutable(): void
    {
        $user = $this->createMock(\Owl\Component\User\Model\UserInterface::class);
        $this->suggestion->setUser($user);
        self::assertSame($user, $this->suggestion->getUser());
    }

    public function testStatusIsMutable(): void
    {
        $this->suggestion->setStatus('new');
        self::assertSame('new', $this->suggestion->getStatus());
    }
} 