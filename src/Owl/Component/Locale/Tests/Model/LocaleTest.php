<?php

declare(strict_types=1);

namespace Owl\Component\Locale\Tests\Model;

use Owl\Component\Locale\Model\Locale;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    private Locale $locale;

    protected function setUp(): void
    {
        $this->locale = new Locale();
    }

    public function testLocaleInitialization(): void
    {
        $this->assertNull($this->locale->getCode());
        $this->assertNotNull($this->locale->getCreatedAt());
        $this->assertNull($this->locale->getUpdatedAt());
    }

    public function testGetterAndSetter(): void
    {
        $this->locale->setCode('en_US');
        $this->assertEquals('en_US', $this->locale->getCode());
    }

    public function testGetName(): void
    {
        $this->locale->setCode('en');
        $this->assertEquals('English', $this->locale->getName());
        
        $this->locale->setCode('pl');
        $this->assertEquals('polski', $this->locale->getName('pl'));
    }

    public function testToString(): void
    {
        $this->locale->setCode('en');
        $this->assertEquals('English', (string) $this->locale);
    }
} 