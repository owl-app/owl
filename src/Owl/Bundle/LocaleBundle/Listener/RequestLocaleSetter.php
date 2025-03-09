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

namespace Owl\Bundle\LocaleBundle\Listener;

use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestLocaleSetter
{
    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
    ) {
        $this->localeContext = $localeContext;
        $this->localeProvider = $localeProvider;
    }

    /**
     * @throws LocaleNotFoundException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $request->setLocale($this->localeContext->getLocaleCode());
        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());
    }
}
