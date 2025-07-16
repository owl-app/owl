<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Tests\Doctrine\ORM\Subscriber;

use Owl\Bundle\RbacBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoadMetadataSubscriberTest extends TestCase
{
    private LoadMetadataSubscriber $subscriber;

    private ClassMetadata&MockObject $metadata;

    private LoadClassMetadataEventArgs&MockObject $eventArgs;

    private string $authItemClass = 'TestAuthItemClass';
    private string $itemTableName = 'test_items';

    protected function setUp(): void
    {
        $this->subscriber = new LoadMetadataSubscriber($this->authItemClass, $this->itemTableName);

        $this->metadata = $this->createMock(ClassMetadata::class);
        $this->eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
    }

    public function testGetSubscribedEventsReturnsCorrectEvents(): void
    {
        $result = $this->subscriber->getSubscribedEvents();

        $this->assertSame(['loadClassMetadata'], $result);
    }

    public function testLoadClassMetadataSetsTableNameWhenClassMatches(): void
    {
        $this->metadata->method('getName')->willReturn($this->authItemClass);
        $this->eventArgs->method('getClassMetadata')->willReturn($this->metadata);

        $this->metadata->expects($this->once())
            ->method('setPrimaryTable')
            ->with(['name' => $this->itemTableName]);

        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testLoadClassMetadataDoesNothingWhenClassDoesNotMatch(): void
    {
        $this->metadata->method('getName')->willReturn('OtherClass');
        $this->eventArgs->method('getClassMetadata')->willReturn($this->metadata);

        $this->metadata->expects($this->never())
            ->method('setPrimaryTable');

        $this->subscriber->loadClassMetadata($this->eventArgs);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->subscriber);
    }
}
