<?php

declare(strict_types=1);

namespace Tests\Owl\Component\File\Model;

use Owl\Component\File\Model\Uploader;
use PHPUnit\Framework\TestCase;

class UploaderTest extends TestCase
{
    private Uploader $uploader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploader = new Uploader();
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->uploader->getId());
    }

    public function testEmailIsNullByDefault(): void
    {
        $this->assertNull($this->uploader->getEmail());
    }

    public function testEmailIsMutable(): void
    {
        $this->uploader->setEmail('john@example.com');
        $this->assertSame('john@example.com', $this->uploader->getEmail());
    }

    public function testFirstNameIsNullByDefault(): void
    {
        $this->assertNull($this->uploader->getFirstName());
    }

    public function testFirstNameIsMutable(): void
    {
        $this->uploader->setFirstName('John');
        $this->assertSame('John', $this->uploader->getFirstName());
    }

    public function testLastNameIsNullByDefault(): void
    {
        $this->assertNull($this->uploader->getLastName());
    }

    public function testLastNameIsMutable(): void
    {
        $this->uploader->setLastName('Doe');
        $this->assertSame('Doe', $this->uploader->getLastName());
    }
}