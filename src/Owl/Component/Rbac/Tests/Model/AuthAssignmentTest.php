<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Model;

use Owl\Component\Rbac\Model\AuthAssignment;
use Owl\Component\Rbac\Model\AuthAssignmentInterface;
use PHPUnit\Framework\TestCase;

final class AuthAssignmentTest extends TestCase
{
    private AuthAssignment $authAssignment;

    protected function setUp(): void
    {
        $this->authAssignment = new AuthAssignment();
    }

    public function testImplementsAuthAssignmentInterface(): void
    {
        self::assertInstanceOf(AuthAssignmentInterface::class, $this->authAssignment);
    }

    public function testHasNoItemNameByDefault(): void
    {
        self::assertNull($this->authAssignment->getItemName());
    }

    public function testItsItemNameIsMutable(): void
    {
        $this->authAssignment->setItemName('admin');
        self::assertSame('admin', $this->authAssignment->getItemName());
    }

    public function testHasCreatedAtSetByDefault(): void
    {
        self::assertInstanceOf(\DateTime::class, $this->authAssignment->getCreatedAt());
    }

    public function testItsUpdatedAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->authAssignment->setUpdatedAt($date);
        self::assertSame($date, $this->authAssignment->getUpdatedAt());
    }
} 