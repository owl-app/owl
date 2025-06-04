<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Tests\Context;

use Owl\Component\Locale\Context\ImmutableLocaleContext;
use PHPUnit\Framework\TestCase;

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
