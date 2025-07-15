<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Owl\Bundle\LocaleBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Owl\Bundle\LocaleBundle\Context\RequestBasedLocaleContext;
use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;
use Owl\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestBasedLocaleContextTest extends TestCase
{
    private RequestStack&MockObject $requestStack;

    /** @var LocaleProviderInterface&MockObject */
    private LocaleProviderInterface&MockObject $localeProvider;

    private RequestBasedLocaleContext $requestBasedLocaleContext;

    private Request&MockObject $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->requestBasedLocaleContext = new RequestBasedLocaleContext($this->requestStack, $this->localeProvider);
        $this->request = $this->createMock(Request::class);
    }

    public function testLocaleContext(): void
    {
        self::assertInstanceOf(LocaleContextInterface::class, $this->requestBasedLocaleContext);
    }

    public function testThrowsLocaleNotFoundExceptionIfMasterRequestIsNotFound(): void
    {
        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn(null);

        self::expectException(LocaleNotFoundException::class);

        $this->requestBasedLocaleContext->getLocaleCode();
    }

    public function testThrowsLocaleNotFoundExceptionIfMasterRequestDoesNotHaveLocaleAttribute(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request->attributes = new ParameterBag();

        self::expectException(LocaleNotFoundException::class);

        $this->requestBasedLocaleContext->getLocaleCode();
    }

    public function testThrowsLocaleNotFoundExceptionIfMasterRequestLocaleCodeIsNotAmongAvailableOnes(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request->attributes = new ParameterBag(['_locale' => 'en_US']);

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::expectException(LocaleNotFoundException::class);

        $this->requestBasedLocaleContext->getLocaleCode();
    }

    public function testReturnsMasterRequestLocaleCode(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request->attributes = new ParameterBag(['_locale' => 'pl_PL']);

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::assertSame('pl_PL', $this->requestBasedLocaleContext->getLocaleCode());
    }
}
