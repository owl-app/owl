<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Locale\Tests\Context;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Owl\Component\Locale\Context\CompositeLocaleContext;
use Owl\Component\Locale\Context\LocaleContextInterface;
use Owl\Component\Locale\Context\LocaleNotFoundException;

class CompositeLocaleContextTest extends TestCase
{
    private CompositeLocaleContext $compositeLocaleContext;

    private LocaleContextInterface&MockObject $firstContext;

    private LocaleContextInterface&MockObject $secondContext;

    protected function setUp(): void
    {
        $this->firstContext = $this->createMock(LocaleContextInterface::class);
        $this->secondContext = $this->createMock(LocaleContextInterface::class);

        $this->compositeLocaleContext = new CompositeLocaleContext();
    }

    public function testGetLocaleCodeFromHighestPriorityContext(): void
    {
        $this->firstContext->expects($this->never())
            ->method('getLocaleCode');

        $this->secondContext = $this->createMock(LocaleContextInterface::class);
        $this->secondContext->expects($this->once())
            ->method('getLocaleCode')
            ->willReturn('en_US');

        $this->compositeLocaleContext->addContext($this->firstContext, 0);
        $this->compositeLocaleContext->addContext($this->secondContext, 1);

        $this->assertEquals('en_US', $this->compositeLocaleContext->getLocaleCode());
    }

    public function testGetLocaleCodeWhenAllContextsThrowException(): void
    {
        $this->firstContext->expects($this->once())
            ->method('getLocaleCode')
            ->willThrowException(new LocaleNotFoundException());

        $this->secondContext = $this->createMock(LocaleContextInterface::class);
        $this->secondContext->expects($this->once())
            ->method('getLocaleCode')
            ->willThrowException(new LocaleNotFoundException());

        $this->compositeLocaleContext->addContext($this->firstContext);
        $this->compositeLocaleContext->addContext($this->secondContext);

        $this->expectException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->getLocaleCode();
    }

    public function testGetLocaleCodeWhenOneContextSucceeds(): void
    {
        $this->firstContext->expects($this->once())
            ->method('getLocaleCode')
            ->willThrowException(new LocaleNotFoundException());

        $this->secondContext = $this->createMock(LocaleContextInterface::class);
        $this->secondContext->expects($this->once())
            ->method('getLocaleCode')
            ->willReturn('fr_FR');

        $this->compositeLocaleContext->addContext($this->firstContext);
        $this->compositeLocaleContext->addContext($this->secondContext);

        $this->assertEquals('fr_FR', $this->compositeLocaleContext->getLocaleCode());
    }
} 