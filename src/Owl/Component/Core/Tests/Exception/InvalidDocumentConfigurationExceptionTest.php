<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Exception;

use Exception;
use Owl\Component\Core\Exception\InvalidDocumentConfigurationException;
use PHPUnit\Framework\TestCase;

final class InvalidDocumentConfigurationExceptionTest extends TestCase
{
    private InvalidDocumentConfigurationException $exception;

    private Exception $previousException;

    protected function setUp(): void
    {
        $this->previousException = new Exception('Previous');
        $this->exception = new InvalidDocumentConfigurationException('error', $this->previousException);
    }

    public function testIsException(): void
    {
        self::assertInstanceOf(Exception::class, $this->exception);
    }

    public function testMessageFormatting(): void
    {
        $this->assertSame('error', $this->exception->getMessage());
    }

    public function testPreviousExceptionIsSet(): void
    {
        $this->assertSame($this->previousException, $this->exception->getPrevious());
    }
}
