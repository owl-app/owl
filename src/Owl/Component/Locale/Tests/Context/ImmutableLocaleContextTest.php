<?php

declare(strict_types=1);

namespace Owl\Component\Locale\Tests\Context;

use PHPUnit\Framework\TestCase;
use Owl\Component\Locale\Context\ImmutableLocaleContext;

class ImmutableLocaleContextTest extends TestCase
{
    public function testGetLocaleCode(): void
    {
        $localeContext = new ImmutableLocaleContext('en_US');
        $this->assertEquals('en_US', $localeContext->getLocaleCode());
    }

    public function testImmutability(): void
    {
        $localeContext = new ImmutableLocaleContext('en_US');
        $this->assertEquals('en_US', $localeContext->getLocaleCode());
        $this->assertEquals('en_US', $localeContext->getLocaleCode());
    }
} 