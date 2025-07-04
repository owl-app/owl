<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory;

use Owl\Component\Core\Factory\SuggestionFactory;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\SuggestionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class SuggestionFactoryTest extends TestCase
{
    private SuggestionFactory $suggestionFactory;

    private FactoryInterface&MockObject $defaultFactory;

    private SuggestionInterface&MockObject $suggestion;

    private AdminUserInterface&MockObject $adminUser;

    protected function setUp(): void
    {
        $this->defaultFactory = $this->createMock(FactoryInterface::class);
        $this->suggestion = $this->createMock(SuggestionInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);

        $this->defaultFactory->method('createNew')->willReturn($this->suggestion);

        $this->suggestionFactory = new SuggestionFactory($this->defaultFactory);
    }

    public function testCreateNew(): void
    {
        $this->defaultFactory->expects($this->once())->method('createNew');

        $result = $this->suggestionFactory->createNew();

        $this->assertSame($this->suggestion, $result);
    }

    public function testCreateAction(): void
    {
        $status = 'new';

        $this->defaultFactory->expects($this->once())->method('createNew');
        $this->suggestion->expects($this->once())->method('setStatus')->with($status);
        $this->suggestion->expects($this->once())->method('setUser')->with($this->adminUser);

        $result = $this->suggestionFactory->createAction($status, $this->adminUser);

        $this->assertSame($this->suggestion, $result);
    }

    public function testCreateActionWithEmptyStatus(): void
    {
        $status = '';

        $this->defaultFactory->expects($this->once())->method('createNew');
        $this->suggestion->expects($this->once())->method('setStatus')->with($status);
        $this->suggestion->expects($this->once())->method('setUser')->with($this->adminUser);

        $result = $this->suggestionFactory->createAction($status, $this->adminUser);

        $this->assertSame($this->suggestion, $result);
    }
}