<?php

declare(strict_types=1);

namespace Owl\Bundle\LocaleBundle\Twig;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Converter\LocaleConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class LocaleExtension extends AbstractExtension
{
    public function __construct(
        private LocaleConverterInterface $localeConverter,
        private LocaleContextInterface $localeContext,
    ) {
    }

    /**
     * @return list{TwigFilter, TwigFilter}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_locale_name', [$this, 'convertCodeToName']),
            new TwigFilter('sylius_locale_country', [$this, 'getCountryCode']),
        ];
    }

    public function convertCodeToName(string $code, ?string $localeCode = null): string
    {
        try {
            return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (\InvalidArgumentException) {
            return $code;
        }
    }

    public function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            return null;
        }
    }

    public function getCountryCode(string $locale): ?string
    {
        return \Locale::getRegion($locale);
    }
}
