<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Context;

use Owl\Bundle\AdminBundle\Context\AdminUserContext;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Model\Rbac\RoleSettingInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminUserContextTest extends TestCase
{
    private AdminUserContext $context;

    private TokenStorageInterface&MockObject $tokenStorage;

    private TokenInterface&MockObject $token;

    private AdminUserInterface&MockObject $adminUser;

    private RoleInterface&MockObject $role;

    private RoleSettingInterface&MockObject $roleSetting;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
        $this->role = $this->createMock(RoleInterface::class);
        $this->roleSetting = $this->createMock(RoleSettingInterface::class);

        $this->context = new AdminUserContext($this->tokenStorage);
    }

    public function testImplementsAdminUserContextInterface(): void
    {
        $this->assertInstanceOf(AdminUserContextInterface::class, $this->context);
    }

    public function testReturnsAdminUserWhenAuthenticated(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        // Act
        $result = $this->context->getUser();

        // Assert
        $this->assertSame($this->adminUser, $result);
    }

    public function testReturnsNullWhenUserIsNotAdminUser(): void
    {
        // Arrange
        $regularUser = $this->createMock(UserInterface::class);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($regularUser);

        // Act
        $result = $this->context->getUser();

        // Assert
        $this->assertNull($result);
    }

    public function testThrowsExceptionEhenNoTokenAvailable(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        // Act & Assert
        $this->expectException(AuthenticationCredentialsNotFoundException::class);
        $this->context->getUser();
    }

    public function testReturnsRoleCanonicalNameWhenUserHasRole(): void
    {
        // Arrange
        $canonicalName = 'ROLE_ADMIN';

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getCanonicalName')
            ->willReturn($canonicalName);

        // Act
        $result = $this->context->getRoleCanonicalName();

        // Assert
        $this->assertSame($canonicalName, $result);
    }

    public function testReturnsNullWhenUserHasNoRole(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn(null);

        // Act
        $result = $this->context->getRoleCanonicalName();

        // Assert
        $this->assertNull($result);
    }

    public function testReturnsNullWhenNoUserAvailable(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        // Act
        $result = $this->context->getRoleCanonicalName();

        // Assert
        $this->assertNull($result);
    }

    public function testReturnsThemeFromRoleSetting(): void
    {
        // Arrange
        $theme = 'dark-theme';

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getSetting')
            ->willReturn($this->roleSetting);

        $this->roleSetting
            ->expects($this->once())
            ->method('getTheme')
            ->willReturn($theme);

        // Act
        $result = $this->context->getTheme();

        // Assert
        $this->assertSame($theme, $result);
    }

    public function testReturnsNullWhenNoRoleSettingAvailable(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getSetting')
            ->willReturn(null);

        // Act
        $result = $this->context->getTheme();

        // Assert
        $this->assertNull($result);
    }

    public function testReturnsTrueWhenUserIsAdminSystem(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getCanonicalName')
            ->willReturn(RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME);

        // Act
        $result = $this->context->isAdminSystem();

        // Assert
        $this->assertTrue($result);
    }

    public function testReturnsFalseWhenUserIsNotAdminSystem(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getCanonicalName')
            ->willReturn('ROLE_USER');

        // Act
        $result = $this->context->isAdminSystem();

        // Assert
        $this->assertFalse($result);
    }

    public function testReturnsTrueWhenUserIsUserRole(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getCanonicalName')
            ->willReturn(RoleAwareInterface::ROLE_USER_NAME);

        // Act
        $result = $this->context->isUser();

        // Assert
        $this->assertTrue($result);
    }

    public function testReturnsFalseWhenUserIsNotUserRole(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->once())
            ->method('getCanonicalName')
            ->willReturn('ROLE_ADMIN');

        // Act
        $result = $this->context->isUser();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function roleScenarioProvider(): array
    {
        return [
            'admin system role' => [
                'roleName' => RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME,
                'expectedIsAdminSystem' => true,
                'expectedIsUser' => false,
            ],
            'user role' => [
                'roleName' => RoleAwareInterface::ROLE_USER_NAME,
                'expectedIsAdminSystem' => false,
                'expectedIsUser' => true,
            ],
            'other role' => [
                'roleName' => 'ROLE_MODERATOR',
                'expectedIsAdminSystem' => false,
                'expectedIsUser' => false,
            ],
        ];
    }

    #[DataProvider('roleScenarioProvider')]
    public function testCorrectlyIdentifiesRoleTypes(string $roleName, bool $expectedIsAdminSystem, bool $expectedIsUser): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->exactly(2))
            ->method('getRole')
            ->willReturn($this->role);

        $this->role
            ->expects($this->exactly(2))
            ->method('getCanonicalName')
            ->willReturn($roleName);

        // Act
        $isAdminSystem = $this->context->isAdminSystem();
        $isUser = $this->context->isUser();

        // Assert
        $this->assertSame($expectedIsAdminSystem, $isAdminSystem);
        $this->assertSame($expectedIsUser, $isUser);
    }

    public function testHandlesNullUserForRoleChecks(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn(null);

        // Act
        $isAdminSystem = $this->context->isAdminSystem();
        $isUser = $this->context->isUser();

        // Assert
        $this->assertFalse($isAdminSystem);
        $this->assertFalse($isUser);
    }

    public function testHandlesUserWithoutRoleForRoleChecks(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->exactly(2))
            ->method('getRole')
            ->willReturn(null);

        // Act
        $isAdminSystem = $this->context->isAdminSystem();
        $isUser = $this->context->isUser();

        // Assert
        $this->assertFalse($isAdminSystem);
        $this->assertFalse($isUser);
    }

    public function testHandlesAuthenticationExceptionInRoleChecks(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        // Act & Assert
        $this->expectException(AuthenticationCredentialsNotFoundException::class);
        $this->context->isAdminSystem();
    }

    public function testHandlesThemeRetrievalWithNoUser(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        // Act
        $result = $this->context->getTheme();

        // Assert
        $this->assertNull($result);
    }

    public function testHandlesThemeRetrievalWithUserWithoutRole(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        $this->adminUser
            ->expects($this->once())
            ->method('getRole')
            ->willReturn(null);

        // Act
        $result = $this->context->getTheme();

        // Assert
        $this->assertNull($result);
    }

    public function testHandlesAuthenticationExceptionInThemeRetrieval(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        // Act & Assert
        $this->expectException(AuthenticationCredentialsNotFoundException::class);
        $this->context->getTheme();
    }

    public function testHandlesAuthenticationExceptionInRoleCanonicalNameRetrieval(): void
    {
        // Arrange
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        // Act & Assert
        $this->expectException(AuthenticationCredentialsNotFoundException::class);
        $this->context->getRoleCanonicalName();
    }
}
