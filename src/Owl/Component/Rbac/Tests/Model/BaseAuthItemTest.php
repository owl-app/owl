<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Model;

use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Model\BaseAuthItem;
use PHPUnit\Framework\TestCase;

final class BaseAuthItemTest extends TestCase
{
    private BaseAuthItem $authItem;

    protected function setUp(): void
    {
        // Tworzę anonimową klasę rozszerzającą BaseAuthItem dla testów
        $this->authItem = new class extends BaseAuthItem {
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

    public function testIdIsEqualToName(): void
    {
        $this->authItem->setName('test-item');
        self::assertSame('test-item', $this->authItem->getId());
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

    public function testHasNoGroupPermissionByDefault(): void
    {
        self::assertNull($this->authItem->getGroupPermission());
    }

    public function testItsGroupPermissionIsMutable(): void
    {
        $this->authItem->setGroupPermission('admin');
        self::assertSame('admin', $this->authItem->getGroupPermission());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        self::assertNull($this->authItem->getDescription());
    }

    public function testItsDescriptionIsMutable(): void
    {
        $this->authItem->setDescription('Test description');
        self::assertSame('Test description', $this->authItem->getDescription());
    }

    public function testHasNoRuleNameByDefault(): void
    {
        self::assertNull($this->authItem->getRuleName());
    }

    public function testItsRuleNameIsMutable(): void
    {
        $this->authItem->setRuleName('test-rule');
        self::assertSame('test-rule', $this->authItem->getRuleName());
    }

    public function testHasCreatedAtSetByDefault(): void
    {
        self::assertInstanceOf(\DateTime::class, $this->authItem->getCreatedAt());
    }

    public function testItsUpdatedAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->authItem->setUpdatedAt($date);
        self::assertSame($date, $this->authItem->getUpdatedAt());
    }
} 