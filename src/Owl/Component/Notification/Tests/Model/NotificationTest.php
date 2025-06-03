<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Notification\Model;

use PHPUnit\Framework\TestCase;
use Owl\Component\Notification\Model\Notification;
use Owl\Component\Notification\Model\NotificationInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

final class NotificationTest extends TestCase
{
    private Notification $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new Notification();
    }

    public function testImplementsNotificationInterface(): void
    {
        self::assertInstanceOf(NotificationInterface::class, $this->notification);
    }

    public function testTimestampable(): void
    {
        self::assertInstanceOf(TimestampableInterface::class, $this->notification);
    }

    public function testDoesNotHaveIdByDefault(): void
    {
        self::assertNull($this->notification->getId());
    }

    public function testHasNoSubjectByDefault(): void
    {
        $this->expectException(\TypeError::class);
        $this->notification->getSubject();
    }

    public function testSubjectIsMutable(): void
    {
        $this->notification->setSubject('Test Subject');
        self::assertSame('Test Subject', $this->notification->getSubject());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        $this->expectException(\TypeError::class);
        $this->notification->getDescription();
    }

    public function testDescriptionIsMutable(): void
    {
        $this->notification->setDescription('Test Description');
        self::assertSame('Test Description', $this->notification->getDescription());
    }

    public function testHasNoDateIssueByDefault(): void
    {
        self::assertNull($this->notification->getDateIssue());
    }

    public function testDateIssueIsMutable(): void
    {
        $date = new \DateTime();
        $this->notification->setDateIssue($date);
        self::assertSame($date, $this->notification->getDateIssue());
    }

    public function testHasNoCurrentFromByDefault(): void
    {
        self::assertNull($this->notification->getCurrentFrom());
    }

    public function testCurrentFromIsMutable(): void
    {
        $date = new \DateTime();
        $this->notification->setCurrentFrom($date);
        self::assertSame($date, $this->notification->getCurrentFrom());
    }

    public function testHasNoCurrentToByDefault(): void
    {
        self::assertNull($this->notification->getCurrentTo());
    }

    public function testCurrentToIsMutable(): void
    {
        $date = new \DateTime();
        $this->notification->setCurrentTo($date);
        self::assertSame($date, $this->notification->getCurrentTo());
    }

    public function testHasCreatedAtByDefault(): void
    {
        self::assertInstanceOf(\DateTime::class, $this->notification->getCreatedAt());
    }

    public function testDoesNotHaveUpdatedAtByDefault(): void
    {
        self::assertNull($this->notification->getUpdatedAt());
    }
} 