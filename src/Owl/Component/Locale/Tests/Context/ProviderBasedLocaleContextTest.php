<?php

declare(strict_types=1);

namespace Owl\Component\Locale\Tests\Context;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Context\ProviderBasedLocaleContext;
use Owl\Component\Locale\Provider\LocaleProviderInterface;

class ProviderBasedLocaleContextTest extends TestCase
{
    private ProviderBasedLocaleContext $localeContext;

    private LocaleProviderInterface&MockObject $localeProvider;

    protected function setUp(): void
    {
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->localeContext = new ProviderBasedLocaleContext($this->localeProvider);
    }

    public function testGetLocaleCodeWhenDefaultLocaleIsAvailable(): void
    {
        $this->localeProvider->expects($this->once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['en_US', 'fr_FR', 'de_DE']);

        $this->localeProvider->expects($this->once())
            ->method('getDefaultLocaleCode')
            ->willReturn('en_US');

        $this->assertEquals('en_US', $this->localeContext->getLocaleCode());
    }

    public function testGetLocaleCodeThrowsExceptionWhenDefaultLocaleIsNotAvailable(): void
    {
        $this->localeProvider->expects($this->once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['fr_FR', 'de_DE']);

        $this->localeProvider->expects($this->once())
            ->method('getDefaultLocaleCode')
            ->willReturn('en_US');

        $this->expectException(LocaleNotFoundException::class);
        $this->expectExceptionMessage('Locale "en_US" is not available! The available ones are: "fr_FR", "de_DE".');

        $this->localeContext->getLocaleCode();
    }
} 