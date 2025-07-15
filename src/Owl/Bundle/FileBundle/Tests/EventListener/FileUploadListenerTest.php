<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Tests\EventListener;

use Owl\Bundle\FileBundle\EventListener\FileUploadListener;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Uploader\FileUploaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\InvalidArgumentException;

final class FileUploadListenerTest extends TestCase
{
    private FileUploaderInterface&MockObject $uploader;
    private FileUploadListener $listener;

    protected function setUp(): void
    {
        $this->uploader = $this->createMock(FileUploaderInterface::class);
        $this->listener = new FileUploadListener($this->uploader);
    }

    public function testUploadsFileWhenResourceHasFile(): void
    {
        // Arrange
        $file = $this->createMock(\SplFileInfo::class);
        $resource = $this->createMock(FileInterface::class);
        $resource->method('getFile')->willReturn($file);

        $event = new GenericEvent($resource);

        $this->uploader->expects($this->once())
            ->method('upload')
            ->with($this->identicalTo($resource));

        // Act
        $this->listener->uploadFile($event);

        // Assert
        // Assertion is handled by the mock expectation
    }

    public function testDoesNotUploadFileWhenResourceHasNoFile(): void
    {
        // Arrange
        $resource = $this->createMock(FileInterface::class);
        $resource->method('getFile')->willReturn(null);

        $event = new GenericEvent($resource);

        $this->uploader->expects($this->never())
            ->method('upload');

        // Act
        $this->listener->uploadFile($event);

        // Assert
        // Assertion is handled by the mock expectation
    }

    public function testThrowsExceptionWhenEventSubjectIsNotFileInterface(): void
    {
        // Arrange
        $invalidResource = new \stdClass();
        $event = new GenericEvent($invalidResource);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    public function testThrowsExceptionWhenEventSubjectIsNull(): void
    {
        // Arrange
        $event = new GenericEvent(null);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    public function testThrowsExceptionWhenEventSubjectIsString(): void
    {
        // Arrange
        $event = new GenericEvent('invalid_subject');

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    public function testThrowsExceptionWhenEventSubjectIsArray(): void
    {
        // Arrange
        $event = new GenericEvent(['invalid' => 'subject']);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    public function testCallsUploaderWithCorrectResource(): void
    {
        // Arrange
        $file = $this->createMock(\SplFileInfo::class);
        $resource = $this->createMock(FileInterface::class);
        $resource->method('getFile')->willReturn($file);

        $event = new GenericEvent($resource);

        $this->uploader->expects($this->once())
            ->method('upload')
            ->with($this->callback(function (FileInterface $uploadedResource) use ($resource) {
                return $uploadedResource === $resource;
            }));

        // Act
        $this->listener->uploadFile($event);

        // Assert
        // Assertion is handled by the mock expectation callback
    }

    public function testHandlesFileInterfaceWithEmptyFileProperly(): void
    {
        // Arrange
        $resource = $this->createMock(FileInterface::class);
        $resource->method('getFile')->willReturn(null);

        $event = new GenericEvent($resource);

        $this->uploader->expects($this->never())
            ->method('upload');

        // Act
        $this->listener->uploadFile($event);

        // Assert
        // Test passes if no exception is thrown and uploader is not called
        $this->addToAssertionCount(1);
    }

    public function testWorksWithDifferentFileTypes(): void
    {
        // Arrange
        $file = new \SplFileInfo(__FILE__);
        $resource = $this->createMock(FileInterface::class);
        $resource->method('getFile')->willReturn($file);

        $event = new GenericEvent($resource);

        $this->uploader->expects($this->once())
            ->method('upload')
            ->with($resource);

        // Act
        $this->listener->uploadFile($event);

        // Assert
        // Assertion is handled by the mock expectation
    }
}
