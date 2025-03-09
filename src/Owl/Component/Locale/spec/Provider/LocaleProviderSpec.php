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

namespace spec\Owl\Component\Locale\Provider;

use Owl\Component\Locale\Model\LocaleInterface;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository): void
    {
        $this->beConstructedWith($localeRepository, 'pl_PL');
    }

    function it_is_a_locale_provider_interface(): void
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_returns_all_enabled_locales(RepositoryInterface $localeRepository, LocaleInterface $locale): void
    {
        $localeRepository->findAll()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_US');

        $this->getAvailableLocalesCodes()->shouldReturn(['en_US']);
    }

    function it_returns_the_default_locale(): void
    {
        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
