<?php

declare(strict_types=1);

namespace Tests\Owl\Component\File\Factory;

use Owl\Component\File\Factory\FileFactory;
use Owl\Component\File\Factory\FileFactoryInterface;
use Owl\Component\File\Model\FileableInterface;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Model\UploaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class FileFactoryTest extends TestCase
{
    /** @var FactoryInterface<FileInterface>&MockObject */
    private FactoryInterface $factory;

    /** @var FileFactory<FileInterface> */
    private FileFactory $fileFactory;

    private FileableInterface&ResourceInterface&Stub $subject;

    private FileInterface&MockObject $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->subject = $this->createStubForIntersectionOfInterfaces([FileableInterface::class, ResourceInterface::class]);
        $this->file = $this->createMock(FileInterface::class);
    
        $this->fileFactory = new FileFactory($this->factory);
        $this->fileFactory->setResourceParents([
            'parentName' => $this->subject
        ]);
    }

    public function testShouldImplementFactoryInterface(): void
    {
        self::assertInstanceOf(FactoryInterface::class, $this->fileFactory);
    }

    public function testShouldImplementFileFactoryInterface(): void
    {
        self::assertInstanceOf(FileFactoryInterface::class, $this->fileFactory);
    }

    public function testCreatesNewFile(): void
    {
        $this->factory->expects($this->once())
            ->method('createNew')
            ->willReturn($this->file);

        self::assertSame($this->file, $this->fileFactory->createNew());
    }

    public function testCreatesFileForParent(): void
    {
        $this->factory->expects($this->once())
            ->method('createNew')
            ->willReturn($this->file);

        $this->file->expects($this->once())
            ->method('setFileSubject')
            ->with($this->subject);

        $result = $this->fileFactory->createForParent('parentName');

        self::assertSame($this->file, $result);
    }

    public function testCreatesFileForSubjectWithUploader(): void
    {
        /** @var UploaderInterface&MockObject $uploader */
        $uploader = $this->createMock(UploaderInterface::class);

        $this->factory->expects($this->once())
            ->method('createNew')
            ->willReturn($this->file);

        $this->file->expects($this->once())
            ->method('setFileSubject')
            ->with($this->subject);

        $this->file->expects($this->once())
            ->method('setAuthor')
            ->with($uploader);

        $result = $this->fileFactory->createForSubjectWithUploader('parentName', $uploader);

        self::assertSame($this->file, $result);
    }
}