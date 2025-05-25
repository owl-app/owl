<?php

declare(strict_types=1);

namespace Tests\Owl\Component\File\Uploader;

use Gaufrette\Filesystem;
use Owl\Component\Core\Filesystem\Exception\FileNotFoundException;
use Owl\Component\File\Generator\FilePathGeneratorInterface;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\File\Uploader\FileUploader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileUploaderTest extends TestCase
{
    private Filesystem&MockObject $filesystem;
    private FilePathGeneratorInterface&MockObject $pathGenerator;
    private FileUploader $uploader;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->pathGenerator = $this->createMock(FilePathGeneratorInterface::class);
        $this->uploader = new FileUploader($this->filesystem, $this->pathGenerator);
    }

    public function testUploadDoesNothingIfNoFile(): void
    {
        $file = $this->createMock(FileInterface::class);
        $file->expects($this->once())->method('hasFile')->willReturn(false);

        $this->filesystem->expects($this->never())->method('write');
        $this->uploader->upload($file);
    }

    public function testUploadRemovesOldFileIfExists(): void
    {
        $file = $this->createMock(FileInterface::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $currentPath = 'old/path';
        $file->method('hasFile')->willReturn(true);
        $file->method('getFile')->willReturn($uploadedFile);
        $file->method('getPath')->willReturnCallback(function () use (&$currentPath) {
            return $currentPath;
        });

        $file->method('setPath')->willReturnCallback(function ($path) use (&$currentPath) {
            $currentPath = $path;
        });

        $uploadedFile->method('getClientOriginalName')->willReturn('original.jpg');
        $uploadedFile->method('getPathname')->willReturn(__FILE__);

        $this->pathGenerator->method('generate')->willReturn('safe/path');

        $this->filesystem->expects($this->any())->method('has')->willReturnCallback(function ($path) {
            return $path === 'old/path';
        });

        $this->filesystem->expects($this->once())->method('delete')->with('old/path')->willReturn(true);

        $this->filesystem->expects($this->once())->method('write')->with(
            'safe/path',
            $this->isString()
        );

        $this->uploader->upload($file);
    }

    public function testUploadSkipsRemoveIfNoOldPath(): void
    {
        $file = $this->createMock(FileInterface::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $currentPath = null;
        $file->method('hasFile')->willReturn(true);
        $file->method('getFile')->willReturn($uploadedFile);
        $file->method('getPath')->willReturnCallback(function () use (&$currentPath) {
            return $currentPath;
        });

        $file->method('setPath')->willReturnCallback(function ($path) use (&$currentPath) {
            $currentPath = $path;
        });

        $uploadedFile->method('getClientOriginalName')->willReturn('original.jpg');
        $uploadedFile->method('getPathname')->willReturn(__FILE__);

        $this->pathGenerator->method('generate')->willReturn('new/path');
        $this->filesystem->method('has')->willReturn(false);

        $this->filesystem->expects($this->once())->method('write')->with(
            'new/path',
            $this->isString()
        );

        $this->uploader->upload($file);
    }

    public function testUploadRegeneratesPathIfAdBlockingOrCollision(): void
    {
        $file = $this->createMock(FileInterface::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $currentPath = null;
        $file->method('hasFile')->willReturn(true);
        $file->method('getFile')->willReturn($uploadedFile);
        $file->method('getPath')->willReturnCallback(function () use (&$currentPath) {
            return $currentPath;
        });

        $file->method('setPath')->willReturnCallback(function ($path) use (&$currentPath) {
            $currentPath = $path;
        });

        $uploadedFile->method('getClientOriginalName')->willReturn('original.jpg');
        $uploadedFile->method('getPathname')->willReturn(__FILE__);

        $this->pathGenerator->expects($this->exactly(3))
            ->method('generate')
            ->willReturnOnConsecutiveCalls('ad/path', 'collision/path', 'ok/path');

        $calls = 0;
        $this->filesystem->expects($this->exactly(2))
            ->method('has')
            ->willReturnCallback(function ($path) use (&$calls) {
                $calls++;
                if ($calls === 1 && $path === 'collision/path') {
                    return true;
                }
                if ($calls === 2 && $path === 'ok/path') {
                    return false;
                }
                return false;
            });

        $this->filesystem->expects($this->once())->method('write')->with(
            'ok/path',
            $this->isString()
        );

        $this->uploader->upload($file);
    }

    public function testRemoveReturnsFalseIfFileDoesNotExist(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('delete')
            ->with('path/to/img')
            ->willThrowException(new FileNotFoundException('path/to/img'));

        $this->assertFalse($this->uploader->remove('path/to/img'));
    }

    public function testRemoveDeletesFileAndReturnsTrue(): void
    {
        $this->filesystem->expects($this->once())->method('delete')->with('path/to/img');

        $this->assertTrue($this->uploader->remove('path/to/img'));
    }

    public function testIsAdBlockingProne(): void
    {
        $reflection = new \ReflectionClass(FileUploader::class);
        $method = $reflection->getMethod('isAdBlockingProne');
        $method->setAccessible(true);

        $uploader = new FileUploader($this->filesystem, $this->pathGenerator);

        $this->assertTrue($method->invoke($uploader, 'path/with/ad/inside'));
        $this->assertTrue($method->invoke($uploader, 'advert/path'));
        $this->assertFalse($method->invoke($uploader, 'normal/path'));
    }
}
