<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Tests\Context;

use PHPUnit\Framework\TestCase;
use Owl\Component\Locale\Context\LocaleNotFoundException;

class LocaleNotFoundExceptionTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $exception = new LocaleNotFoundException();
        $this->assertEquals('Locale could not be found!', $exception->getMessage());
    }

    public function testCustomMessage(): void
    {
        $exception = new LocaleNotFoundException('Custom message');
        $this->assertEquals('Custom message', $exception->getMessage());
    }

    public function testNotFoundStaticConstructor(): void
    {
        $exception = LocaleNotFoundException::notFound('fr_FR');
        $this->assertEquals('Locale "fr_FR" cannot be found!', $exception->getMessage());
    }

    public function testNotAvailableStaticConstructor(): void
    {
        $exception = LocaleNotFoundException::notAvailable('fr_FR', ['en_US', 'de_DE']);
        $this->assertEquals(
            'Locale "fr_FR" is not available! The available ones are: "en_US", "de_DE".',
            $exception->getMessage()
        );
    }

    public function testPreviousException(): void
    {
        $previousException = new \Exception('Previous error');
        $exception = new LocaleNotFoundException(null, $previousException);
        $this->assertSame($previousException, $exception->getPrevious());
    }
} 