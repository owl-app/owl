<?php

declare(strict_types=1);

namespace Tests\Owl\Component\File\Model;

use Owl\Component\File\Model\File;
use Owl\Component\File\Model\FileableInterface;
use Owl\Component\File\Model\UploaderInterface;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private File $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = new File();
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->file->getId());
    }

    public function testTypeIsNullByDefault(): void
    {
        $this->assertNull($this->file->getType());
    }

    public function testTypeIsMutable(): void
    {
        $this->file->setType('invoice');
        $this->assertSame('invoice', $this->file->getType());
    }

    public function testFileIsNullByDefault(): void
    {
        $this->assertNull($this->file->getFile());
    }

    public function testFileIsMutable(): void
    {
        $splFile = new \SplFileInfo(__FILE__);
        $this->file->setFile($splFile);
        $this->assertSame($splFile, $this->file->getFile());
    }

    public function testHasFile(): void
    {
        $this->assertFalse($this->file->hasFile());
        $splFile = new \SplFileInfo(__FILE__);
        $this->file->setFile($splFile);
        $this->assertTrue($this->file->hasFile());
    }

    public function testPathIsNullByDefault(): void
    {
        $this->assertNull($this->file->getPath());
    }

    public function testPathIsMutable(): void
    {
        $this->file->setPath('/tmp/file.txt');
        $this->assertSame('/tmp/file.txt', $this->file->getPath());
    }

    public function testHasPath(): void
    {
        $this->assertFalse($this->file->hasPath());
        $this->file->setPath('/tmp/file.txt');
        $this->assertTrue($this->file->hasPath());
    }

    public function testNameIsNullByDefault(): void
    {
        $this->assertNull($this->file->getName());
    }

    public function testNameIsMutable(): void
    {
        $this->file->setName('Document');
        $this->assertSame('Document', $this->file->getName());
    }

    public function testDescriptionIsNullByDefault(): void
    {
        $this->assertNull($this->file->getDescription());
    }

    public function testDescriptionIsMutable(): void
    {
        $this->file->setDescription('Test description');
        $this->assertSame('Test description', $this->file->getDescription());
    }

    public function testAuthorIsNullByDefault(): void
    {
        $this->assertNull($this->file->getAuthor());
    }

    public function testAuthorIsMutable(): void
    {
        $author = $this->createMock(UploaderInterface::class);
        $this->file->setAuthor($author);
        $this->assertSame($author, $this->file->getAuthor());
    }

    public function testFileSubjectIsNullByDefault(): void
    {
        $this->assertNull($this->file->getFileSubject());
    }

    public function testFileSubjectIsMutable(): void
    {
        $subject = $this->createMock(FileableInterface::class);
        $this->file->setFileSubject($subject);
        $this->assertSame($subject, $this->file->getFileSubject());
    }

    public function testCreatedAtIsInitialized(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->file->getCreatedAt());
    }

    public function testCreatedAtIsMutable(): void
    {
        $date = new \DateTimeImmutable('2024-06-01 12:00:00');
        $this->file->setCreatedAt($date);
        $this->assertSame($date, $this->file->getCreatedAt());
    }

    public function testUpdatedAtIsNullByDefault(): void
    {
        $this->assertNull($this->file->getUpdatedAt());
    }

    public function testUpdatedAtIsMutable(): void
    {
        $date = new \DateTimeImmutable('2024-06-01 13:00:00');
        $this->file->setUpdatedAt($date);
        $this->assertSame($date, $this->file->getUpdatedAt());
    }
}
