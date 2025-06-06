<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Model;

use Owl\Component\Rbac\Model\AuthItem;
use Owl\Component\Rbac\Model\AuthItemInterface;
use PHPUnit\Framework\TestCase;

final class AuthItemTest extends TestCase
{
    private AuthItem $authItem;

    protected function setUp(): void
    {
        // Tworzę anonimową klasę rozszerzającą AuthItem dla testów
        $this->authItem = new class extends AuthItem {
            public function getType(): string
            {
                return 'test';
            }
        };
    }

    public function testImplementsAuthItemInterface(): void
    {
        self::assertInstanceOf(AuthItemInterface::class, $this->authItem);
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->authItem->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->authItem->setName('test-item');
        self::assertSame('test-item', $this->authItem->getName());
    }
} 