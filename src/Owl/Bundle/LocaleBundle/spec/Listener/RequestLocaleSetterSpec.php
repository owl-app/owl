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

namespace spec\Owl\Bundle\LocaleBundle\Listener;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestLocaleSetterSpec extends ObjectBehavior
{
    public function let(LocaleContextInterface $localeContext, LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeContext, $localeProvider);
    }

    public function it_sets_locale_and_default_locale_on_request(
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        RequestEvent $event,
        Request $request,
    ): void {
        $event->getRequest()->willReturn($request);

        $localeContext->getLocaleCode()->willReturn('pl_PL');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $request->setLocale('pl_PL')->shouldBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
