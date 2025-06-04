<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Exception;

use Owl\Component\Core\Exception\HandleException;
use PHPUnit\Framework\TestCase;

final class HandleExceptionTest extends TestCase
{
    private HandleException $exception;

    protected function setUp(): void
    {
        $this->exception = new HandleException(HandleException::class, 'request does not have locale code');
    }

    public function testIsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->exception);
    }

    public function testMessageFormatting(): void
    {
        $this->assertSame(
            sprintf('%s was unable to handle this request. request does not have locale code', HandleException::class),
            $this->exception->getMessage(),
        );
    }

    public function testPreviousExceptionIsSet(): void
    {
        $previous = new \Exception('Previous');
        $exception = new HandleException('Handler', 'Error', $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testEmptyHandlerNameAndMessage(): void
    {
        $exception = new HandleException('', '', null);
        $this->assertSame(' was unable to handle this request. ', $exception->getMessage());
    }
}
