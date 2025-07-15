<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Tests\EventListener;

use Owl\Bundle\FileBundle\EventListener\FileUploadListener;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Uploader\FileUploaderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(FileUploadListener::class)]
final class FileUploadListenerTest extends TestCase
{
    private FileUploaderInterface&MockObject $uploader;
    private FileUploadListener $listener;

    protected function setUp(): void
    {
        $this->uploader = $this->createMock(FileUploaderInterface::class);
        $this->listener = new FileUploadListener($this->uploader);
    }

    #[Test]
    public function it_uploads_file_when_resource_has_file(): void
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

    #[Test]
    public function it_does_not_upload_file_when_resource_has_no_file(): void
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

    #[Test]
    public function it_throws_exception_when_event_subject_is_not_file_interface(): void
    {
        // Arrange
        $invalidResource = new \stdClass();
        $event = new GenericEvent($invalidResource);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    #[Test]
    public function it_throws_exception_when_event_subject_is_null(): void
    {
        // Arrange
        $event = new GenericEvent(null);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    #[Test]
    public function it_throws_exception_when_event_subject_is_string(): void
    {
        // Arrange
        $event = new GenericEvent('invalid_subject');

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    #[Test]
    public function it_throws_exception_when_event_subject_is_array(): void
    {
        // Arrange
        $event = new GenericEvent(['invalid' => 'subject']);

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->listener->uploadFile($event);
    }

    #[Test]
    public function it_calls_uploader_with_correct_resource(): void
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

    #[Test]
    public function it_handles_file_interface_with_empty_file_properly(): void
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

    #[Test]
    public function it_works_with_different_file_types(): void
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
