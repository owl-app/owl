<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Manager;

use Doctrine\Persistence\ObjectManager;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Manager\UserPreferenceManager;
use Owl\Component\Core\Model\AdminUserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserPreferenceManagerTest extends TestCase
{
    private UserPreferenceManager $userPreferenceManager;

    private AdminUserContextInterface|MockObject $adminUserContext;

    private ObjectManager|MockObject $objectManager;

    private AdminUserInterface|MockObject $adminUser;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);

        $this->userPreferenceManager = new UserPreferenceManager(
            $this->adminUserContext,
            $this->objectManager,
        );
    }

    public function testUpdateWhenUserIsNull(): void
    {
        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $this->objectManager->expects(self::never())
            ->method('persist');

        $this->objectManager->expects(self::never())
            ->method('flush');

        $this->userPreferenceManager->update('test.key', 'test_value');
    }

    public function testUpdateWithValidUser(): void
    {
        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn([]);

        $this->adminUser->expects(self::once())
            ->method('setPreferences')
            ->with(['test' => ['key' => 'test_value']]);

        $this->objectManager->expects(self::once())
            ->method('persist')
            ->with($this->adminUser);

        $this->objectManager->expects(self::once())
            ->method('flush');

        $this->userPreferenceManager->update('test.key', 'test_value');
    }

    public function testUpdateWithExistingPreferences(): void
    {
        $existingPreferences = ['existing' => 'value', 'test' => ['other' => 'value']];
        $expectedPreferences = ['existing' => 'value', 'test' => ['other' => 'value', 'key' => 'test_value']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($existingPreferences);

        $this->adminUser->expects(self::once())
            ->method('setPreferences')
            ->with($expectedPreferences);

        $this->objectManager->expects(self::once())
            ->method('persist')
            ->with($this->adminUser);

        $this->objectManager->expects(self::once())
            ->method('flush');

        $this->userPreferenceManager->update('test.key', 'test_value');
    }

    public function testGetWhenUserIsNull(): void
    {
        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $result = $this->userPreferenceManager->get('test.key');

        self::assertNull($result);
    }

    public function testGetWithExistingKey(): void
    {
        $preferences = ['test' => ['key' => 'test_value']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->get('test.key');

        self::assertSame('test_value', $result);
    }

    public function testGetWithNonExistingKey(): void
    {
        $preferences = ['other' => ['key' => 'other_value']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->get('test.key');

        self::assertNull($result);
    }

    public function testHasWithExistingKey(): void
    {
        $preferences = ['test' => ['key' => 'test_value']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->has('test.key');

        self::assertTrue($result);
    }

    public function testHasWithNonExistingKey(): void
    {
        $preferences = ['other' => ['key' => 'other_value']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->has('test.key');

        self::assertFalse($result);
    }

    public function testHasWithEmptyValue(): void
    {
        $preferences = ['test' => ['key' => '']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->has('test.key');

        self::assertFalse($result);
    }

    public function testHasWithNullValue(): void
    {
        $preferences = ['test' => ['key' => null]];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->has('test.key');

        self::assertFalse($result);
    }

    public function testHasWhenUserIsNull(): void
    {
        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $result = $this->userPreferenceManager->has('test.key');

        self::assertFalse($result);
    }

    public function testUpdateWithNestedKeys(): void
    {
        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn([]);

        $this->adminUser->expects(self::once())
            ->method('setPreferences')
            ->with(['very' => ['deep' => ['nested' => ['key' => 'value']]]]);

        $this->objectManager->expects(self::once())
            ->method('persist')
            ->with($this->adminUser);

        $this->objectManager->expects(self::once())
            ->method('flush');

        $this->userPreferenceManager->update('very.deep.nested.key', 'value');
    }

    public function testGetWithNestedKeys(): void
    {
        $preferences = ['very' => ['deep' => ['nested' => ['key' => 'value']]]];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->get('very.deep.nested.key');

        self::assertSame('value', $result);
    }

    public function testGetWithInvalidPathInMiddle(): void
    {
        $preferences = ['very' => ['deep' => 'not_an_array']];

        $this->adminUserContext->expects(self::once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser->expects(self::once())
            ->method('getPreferences')
            ->willReturn($preferences);

        $result = $this->userPreferenceManager->get('very.deep.nested.key');

        self::assertNull($result);
    }
}
