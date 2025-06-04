<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Filesystem\Exception;

use Exception;
use Owl\Component\Core\Filesystem\Exception\FileNotFoundException;
use PHPUnit\Framework\TestCase;

final class FileNotFoundExceptionTest extends TestCase
{
    private FileNotFoundException $exception;

    private Exception $previousException;

    protected function setUp(): void
    {
        $this->previousException = new Exception('Previous');
        $this->exception = new FileNotFoundException('file_test_path', $this->previousException);
    }

    public function testIsRuntimeException(): void
    {
        $this->assertInstanceOf(Exception::class, $this->exception);
    }

    public function testMessageFormatting(): void
    {
        $this->assertSame(
            sprintf('File "%s" could not be found.', 'file_test_path'),
            $this->exception->getMessage(),
        );
    }

    public function testPreviousExceptionIsSet(): void
    {
        $this->assertSame($this->previousException, $this->exception->getPrevious());
    }
}
