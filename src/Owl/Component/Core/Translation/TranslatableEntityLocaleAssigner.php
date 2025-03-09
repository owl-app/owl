<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\Core\Translation;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var TranslationLocaleProviderInterface */
    private $translationLocaleProvider;

    public function __construct(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
    ) {
        $this->localeContext = $localeContext;
        $this->translationLocaleProvider = $translationLocaleProvider;
    }

    public function assignLocale(TranslatableInterface $translatableEntity): void
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $e) {
            $currentLocale = $fallbackLocale;
        }

        $translatableEntity->setCurrentLocale($currentLocale);
        $translatableEntity->setFallbackLocale($fallbackLocale);
    }
}
