<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Tests\Provider;

use Owl\Component\Locale\Model\LocaleInterface;
use Owl\Component\Locale\Provider\LocaleProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class LocaleProviderTest extends TestCase
{
    private RepositoryInterface $localeRepository;

    private LocaleProvider $provider;

    private string $defaultLocaleCode = 'en_US';

    protected function setUp(): void
    {
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->provider = new LocaleProvider($this->localeRepository, $this->defaultLocaleCode);
    }

    public function testGetAvailableLocalesCodes(): void
    {
        $locale1 = $this->createMock(LocaleInterface::class);
        $locale1->expects($this->once())
            ->method('getCode')
            ->willReturn('en_US');

        $locale2 = $this->createMock(LocaleInterface::class);
        $locale2->expects($this->once())
            ->method('getCode')
            ->willReturn('fr_FR');

        $this->localeRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$locale1, $locale2]);

        $this->assertEquals(['en_US', 'fr_FR'], $this->provider->getAvailableLocalesCodes());
    }

    public function testGetDefaultLocaleCode(): void
    {
        $this->assertEquals($this->defaultLocaleCode, $this->provider->getDefaultLocaleCode());
    }
}
