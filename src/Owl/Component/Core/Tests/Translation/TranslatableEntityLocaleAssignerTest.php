<?php

declare(strict_types=1);

namespace Owl\Component\Core\Tests\Translation;

use Owl\Component\Core\Translation\TranslatableEntityLocaleAssigner;
use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Resource\Model\TranslatableInterface;

class TranslatableEntityLocaleAssignerTest extends TestCase
{
    /**
     * @var LocaleContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $localeContext;

    /**
     * @var TranslationLocaleProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $translationLocaleProvider;

    /**
     * @var TranslatableInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $translatableEntity;

    private TranslatableEntityLocaleAssigner $localeAssigner;

    protected function setUp(): void
    {
        $this->localeContext = $this->getMockBuilder(LocaleContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translationLocaleProvider = $this->getMockBuilder(TranslationLocaleProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translatableEntity = $this->getMockBuilder(TranslatableInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->localeAssigner = new TranslatableEntityLocaleAssigner(
            $this->localeContext,
            $this->translationLocaleProvider
        );
    }

    public function testAssignLocaleWhenCurrentLocaleIsAvailable(): void
    {
        // Przygotowanie
        $currentLocale = 'pl_PL';
        $fallbackLocale = 'en_US';

        $this->localeContext
            ->method('getLocaleCode')
            ->willReturn($currentLocale);

        $this->translationLocaleProvider
            ->method('getDefaultLocaleCode')
            ->willReturn($fallbackLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setCurrentLocale')
            ->with($currentLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setFallbackLocale')
            ->with($fallbackLocale);

        // Wykonanie
        $this->localeAssigner->assignLocale($this->translatableEntity);
    }

    public function testAssignLocaleWhenCurrentLocaleIsNotAvailable(): void
    {
        // Przygotowanie
        $fallbackLocale = 'en_US';

        $this->localeContext
            ->method('getLocaleCode')
            ->willThrowException(new LocaleNotFoundException());

        $this->translationLocaleProvider
            ->method('getDefaultLocaleCode')
            ->willReturn($fallbackLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setCurrentLocale')
            ->with($fallbackLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setFallbackLocale')
            ->with($fallbackLocale);

        // Wykonanie
        $this->localeAssigner->assignLocale($this->translatableEntity);
    }

    public function testAssignLocaleWithDifferentLocales(): void
    {
        // Przygotowanie
        $currentLocale = 'de_DE';
        $fallbackLocale = 'fr_FR';

        $this->localeContext
            ->method('getLocaleCode')
            ->willReturn($currentLocale);

        $this->translationLocaleProvider
            ->method('getDefaultLocaleCode')
            ->willReturn($fallbackLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setCurrentLocale')
            ->with($currentLocale);

        $this->translatableEntity
            ->expects($this->once())
            ->method('setFallbackLocale')
            ->with($fallbackLocale);

        // Wykonanie
        $this->localeAssigner->assignLocale($this->translatableEntity);
    }
} 