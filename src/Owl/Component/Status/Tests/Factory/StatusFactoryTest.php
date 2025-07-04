<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Status\Factory;

use Owl\Bridge\SyliusResource\Exception\ParetResourceNotFound;
use Owl\Bridge\SyliusResource\Factory\Resource\ParentableFactory;
use Owl\Component\Status\Factory\StatusFactory;
use Owl\Component\Status\Factory\StatusFactoryInterface;
use Owl\Component\Status\Model\OwnerInterface;
use Owl\Component\Status\Model\StatusableInterface;
use Owl\Component\Status\Model\StatusInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class StatusFactoryTest extends TestCase
{
    private StatusFactory $statusFactory;

    private FactoryInterface&MockObject $decoratedFactory;

    private StatusInterface&MockObject $status;

    private StatusableInterface&MockObject $statusSubject;

    private OwnerInterface&MockObject $owner;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->status = $this->createMock(StatusInterface::class);
        $this->statusSubject = $this->createMock(StatusableInterface::class);
        $this->owner = $this->createMock(OwnerInterface::class);

        $this->statusFactory = new StatusFactory($this->decoratedFactory);
    }

    public function testImplementsFactoryInterface(): void
    {
        self::assertInstanceOf(FactoryInterface::class, $this->statusFactory);
    }

    public function testImplementsStatusFactoryInterface(): void
    {
        self::assertInstanceOf(StatusFactoryInterface::class, $this->statusFactory);
    }

    public function testExtendsParentableFactory(): void
    {
        self::assertInstanceOf(ParentableFactory::class, $this->statusFactory);
    }

    public function testItIsInitializedWithFactory(): void
    {
        $factory = new StatusFactory($this->decoratedFactory);

        self::assertInstanceOf(StatusFactory::class, $factory);
    }

    public function testCreateNew(): void
    {
        $this->decoratedFactory
            ->method('createNew')
            ->willReturn($this->status);

        $result = $this->statusFactory->createNew();

        self::assertSame($this->status, $result);
    }

    public function testCreateForParent(): void
    {
        $parentName = 'parent_name';

        // Przygotowanie mocków
        $this->decoratedFactory
            ->method('createNew')
            ->willReturn($this->status);

        $this->status
            ->expects($this->once())
            ->method('setStatusSubject')
            ->with($this->statusSubject);

        // Ustawienie resource parent
        $this->statusFactory->setResourceParents([$parentName => $this->statusSubject]);

        $result = $this->statusFactory->createForParent($parentName);

        self::assertSame($this->status, $result);
    }

    public function testCreateForParentThrowsExceptionWhenParentNotFound(): void
    {
        $this->expectException(ParetResourceNotFound::class);
        $this->expectExceptionMessage('Resource non_existing_parent not found');

        $this->statusFactory->createForParent('non_existing_parent');
    }

    public function testCreateForSubjectWithOwner(): void
    {
        $parentName = 'parent_name';

        // Przygotowanie mocków
        $this->decoratedFactory
            ->method('createNew')
            ->willReturn($this->status);

        $this->status
            ->expects($this->once())
            ->method('setStatusSubject')
            ->with($this->statusSubject);

        $this->status
            ->expects($this->once())
            ->method('setOwner')
            ->with($this->owner);

        // Ustawienie resource parent
        $this->statusFactory->setResourceParents([$parentName => $this->statusSubject]);

        $result = $this->statusFactory->createForSubjectWithOwner($parentName, $this->owner);

        self::assertSame($this->status, $result);
    }

    public function testCreateForSubjectWithNullOwner(): void
    {
        $parentName = 'parent_name';

        // Przygotowanie mocków
        $this->decoratedFactory
            ->method('createNew')
            ->willReturn($this->status);

        $this->status
            ->expects($this->once())
            ->method('setStatusSubject')
            ->with($this->statusSubject);

        $this->status
            ->expects($this->once())
            ->method('setOwner')
            ->with(null);

        // Ustawienie resource parent
        $this->statusFactory->setResourceParents([$parentName => $this->statusSubject]);

        $result = $this->statusFactory->createForSubjectWithOwner($parentName, null);

        self::assertSame($this->status, $result);
    }
}
