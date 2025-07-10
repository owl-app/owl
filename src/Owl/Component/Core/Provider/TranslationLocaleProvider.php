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

namespace Owl\Component\Core\Provider;

use Owl\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    private RepositoryInterface $localeRepository;

    private string $defaultLocaleCode;

    public function __construct(RepositoryInterface $localeRepository, string $defaultLocaleCode)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    public function getDefinedLocalesCodes(): array
    {
        /** @var array<LocaleInterface> $locales */
        $locales = $this->localeRepository->findAll();

        return array_map(
            function (LocaleInterface $locale): string {
                return (string) $locale->getCode();
            },
            $locales,
        );
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}
