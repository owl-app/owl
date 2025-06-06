<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Provider;

use Owl\Component\Core\Provider\TranslationLocaleProvider;
use Owl\Component\Locale\Model\LocaleInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class TranslationLocaleProviderTest extends TestCase
{
    private RepositoryInterface&MockObject $localeRepository;

    private TranslationLocaleProvider $provider;

    protected function setUp(): void
    {
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new TranslationLocaleProvider($this->localeRepository, 'en_US');
    }

    public function testGetDefinedLocalesCodesReturnsArrayOfLocaleCodes(): void
    {
        $locale1 = $this->createMockLocale('en_US');
        $locale2 = $this->createMockLocale('pl_PL');
        $locale3 = $this->createMockLocale('de_DE');

        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale1, $locale2, $locale3]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals(['en_US', 'pl_PL', 'de_DE'], $result);
    }

    public function testGetDefinedLocalesCodesReturnsEmptyArrayWhenNoLocales(): void
    {
        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals([], $result);
    }

    public function testGetDefinedLocalesCodesHandlesNullLocaleCodes(): void
    {
        $locale1 = $this->createMockLocale('en_US');
        $locale2 = $this->createMockLocale(null);
        $locale3 = $this->createMockLocale('pl_PL');

        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale1, $locale2, $locale3]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals(['en_US', '', 'pl_PL'], $result);
    }

    public function testGetDefinedLocalesCodesHandlesEmptyLocaleCodes(): void
    {
        $locale1 = $this->createMockLocale('en_US');
        $locale2 = $this->createMockLocale('');
        $locale3 = $this->createMockLocale('pl_PL');

        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale1, $locale2, $locale3]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals(['en_US', '', 'pl_PL'], $result);
    }

    public function testGetDefaultLocaleCodeReturnsDefaultLocale(): void
    {
        $result = $this->provider->getDefaultLocaleCode();

        $this->assertEquals('en_US', $result);
    }

    public function testGetDefaultLocaleCodeWithDifferentDefaultLocale(): void
    {
        $provider = new TranslationLocaleProvider($this->localeRepository, 'fr_FR');

        $result = $provider->getDefaultLocaleCode();

        $this->assertEquals('fr_FR', $result);
    }

    public function testGetDefaultLocaleCodeWithEmptyDefaultLocale(): void
    {
        $provider = new TranslationLocaleProvider($this->localeRepository, '');

        $result = $provider->getDefaultLocaleCode();

        $this->assertEquals('', $result);
    }

    public function testConstructorWithEmptyDefaultLocaleCode(): void
    {
        $provider = new TranslationLocaleProvider($this->localeRepository, '');

        $this->assertInstanceOf(TranslationLocaleProvider::class, $provider);
        $this->assertEquals('', $provider->getDefaultLocaleCode());
    }

    public function testGetDefinedLocalesCodesWithSingleLocale(): void
    {
        $locale = $this->createMockLocale('en_US');

        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals(['en_US'], $result);
    }

    public function testGetDefinedLocalesCodesWithSpecialCharacters(): void
    {
        $locale1 = $this->createMockLocale('zh_CN');
        $locale2 = $this->createMockLocale('es_ES@valencia');
        $locale3 = $this->createMockLocale('sr_RS@latin');

        $this->localeRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale1, $locale2, $locale3]);

        $result = $this->provider->getDefinedLocalesCodes();

        $this->assertEquals(['zh_CN', 'es_ES@valencia', 'sr_RS@latin'], $result);
    }

    private function createMockLocale(?string $code): LocaleInterface
    {
        $locale = $this->createMock(LocaleInterface::class);
        $locale->method('getCode')->willReturn($code);

        return $locale;
    }
}
