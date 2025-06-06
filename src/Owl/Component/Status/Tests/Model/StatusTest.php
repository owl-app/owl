<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Status\Model;

use Owl\Component\Status\Model\OwnerInterface;
use Owl\Component\Status\Model\Status;
use Owl\Component\Status\Model\StatusableInterface;
use Owl\Component\Status\Model\StatusInterface;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    private Status $status;

    protected function setUp(): void
    {
        $this->status = new class() extends Status {
            public function getStatusesLabels(): array
            {
                return [
                    'new' => 'Nowy',
                    'in_progress' => 'W trakcie',
                    'done' => 'Zakończony',
                ];
            }
        };
    }

    public function testImplementsStatusInterface(): void
    {
        self::assertInstanceOf(StatusInterface::class, $this->status);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->status->getId());
    }

    public function testHasNoStatusByDefault(): void
    {
        self::expectException(\TypeError::class);
        $this->status->getStatus();
    }

    public function testItsStatusIsMutable(): void
    {
        $this->status->setStatus('new');
        self::assertSame('new', $this->status->getStatus());
    }

    public function testHasNoCommentByDefault(): void
    {
        self::assertNull($this->status->getComment());
    }

    public function testItsCommentIsMutable(): void
    {
        $this->status->setComment('Test comment');
        self::assertSame('Test comment', $this->status->getComment());
    }

    public function testCommentCanBeNull(): void
    {
        $this->status->setComment('Test comment');
        $this->status->setComment(null);
        self::assertNull($this->status->getComment());
    }

    public function testHasNoOwnerByDefault(): void
    {
        self::assertNull($this->status->getOwner());
    }

    public function testItsOwnerIsMutable(): void
    {
        $owner = $this->createMock(OwnerInterface::class);
        $this->status->setOwner($owner);
        self::assertSame($owner, $this->status->getOwner());
    }

    public function testOwnerCanBeNull(): void
    {
        $owner = $this->createMock(OwnerInterface::class);
        $this->status->setOwner($owner);
        $this->status->setOwner(null);
        self::assertNull($this->status->getOwner());
    }

    public function testHasNoStatusSubjectByDefault(): void
    {
        self::assertNull($this->status->getStatusSubject());
    }

    public function testItsStatusSubjectIsMutable(): void
    {
        $statusSubject = $this->createMock(StatusableInterface::class);
        $this->status->setStatusSubject($statusSubject);
        self::assertSame($statusSubject, $this->status->getStatusSubject());
    }

    public function testStatusSubjectCanBeNull(): void
    {
        $statusSubject = $this->createMock(StatusableInterface::class);
        $this->status->setStatusSubject($statusSubject);
        $this->status->setStatusSubject(null);
        self::assertNull($this->status->getStatusSubject());
    }

    public function testHasCreatedAtByDefault(): void
    {
        self::assertInstanceOf(\DateTime::class, $this->status->getCreatedAt());
    }

    public function testHasNoUpdatedAtByDefault(): void
    {
        self::assertNull($this->status->getUpdatedAt());
    }

    public function testItsUpdatedAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->status->setUpdatedAt($date);
        self::assertSame($date, $this->status->getUpdatedAt());
    }

    public function testReturnsStatusLabels(): void
    {
        $expectedLabels = [
            'new' => 'Nowy',
            'in_progress' => 'W trakcie',
            'done' => 'Zakończony',
        ];

        self::assertSame($expectedLabels, $this->status->getStatusesLabels());
    }
}
