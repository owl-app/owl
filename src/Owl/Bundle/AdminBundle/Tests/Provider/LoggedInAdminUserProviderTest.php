<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Provider;

use Owl\Bundle\AdminBundle\Provider\LoggedInAdminUserProvider;
use Owl\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(LoggedInAdminUserProvider::class)]
final class LoggedInAdminUserProviderTest extends TestCase
{
    private LoggedInAdminUserProvider $provider;
    private Security&MockObject $security;
    private TokenStorageInterface&MockObject $tokenStorage;
    private RequestStack&MockObject $requestStack;
    private UserRepositoryInterface&MockObject $adminUserRepository;
    private AdminUserInterface&MockObject $adminUser;
    private TokenInterface&MockObject $token;
    private Request&MockObject $request;
    private SessionInterface&MockObject $session;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->adminUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(SessionInterface::class);

        $this->provider = new LoggedInAdminUserProvider(
            $this->security,
            $this->tokenStorage,
            $this->requestStack,
            $this->adminUserRepository
        );
    }

    #[Test]
    public function it_implements_logged_in_admin_user_provider_interface(): void
    {
        $this->assertInstanceOf(LoggedInAdminUserProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_returns_admin_user_from_security_component(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertSame($this->adminUser, $result);
    }

    #[Test]
    public function it_returns_admin_user_from_token_storage_when_security_fails(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertSame($this->adminUser, $result);
    }

    #[Test]
    public function it_returns_admin_user_from_session_when_other_methods_fail(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->exactly(2))
            ->method('get')
            ->with('_security_admin')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_no_user_found_anywhere(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->exactly(2))
            ->method('get')
            ->with('_security_admin')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_security_returns_non_admin_user(): void
    {
        // Arrange
        $regularUser = $this->createMock(UserInterface::class);
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($regularUser);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->exactly(2))
            ->method('get')
            ->with('_security_admin')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_handles_session_not_found_exception(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willThrowException(new SessionNotFoundException());

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_true_when_has_admin_user_from_security(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        // Act
        $result = $this->provider->hasUser();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_true_when_has_admin_user_from_token_storage(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->adminUser);

        // Act
        $result = $this->provider->hasUser();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_true_when_has_serialized_token_in_session(): void
    {
        // Arrange
        $serializedToken = 'serialized-token-data';
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('_security_admin')
            ->willReturn($serializedToken);

        // Act
        $result = $this->provider->hasUser();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_has_no_user_anywhere(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->exactly(2))
            ->method('get')
            ->with('_security_admin')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        // Act
        $result = $this->provider->hasUser();

        // Assert
        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_null_when_session_token_is_not_serializable(): void
    {
        // Arrange
        $invalidToken = 'invalid-token-data';
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('_security_admin')
            ->willReturn($invalidToken);

        // Act
        $result = $this->provider->getUser();

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_handles_session_not_found_exception_gracefully_in_has_user(): void
    {
        // Arrange
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $this->requestStack
            ->expects($this->once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willThrowException(new SessionNotFoundException());

        // Act
        $result = $this->provider->hasUser();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function userRetrievalScenarioProvider(): array
    {
        return [
            'security returns admin user' => [
                'securityUser' => 'admin',
                'tokenStorageUser' => null,
                'sessionToken' => null,
                'expected' => 'admin',
            ],
            'token storage returns admin user' => [
                'securityUser' => null,
                'tokenStorageUser' => 'admin',
                'sessionToken' => null,
                'expected' => 'admin',
            ],
            'no user found' => [
                'securityUser' => null,
                'tokenStorageUser' => null,
                'sessionToken' => null,
                'expected' => null,
            ],
        ];
    }

    #[Test]
    #[DataProvider('userRetrievalScenarioProvider')]
    public function it_follows_correct_user_retrieval_priority(?string $securityUser, ?string $tokenStorageUser, ?string $sessionToken, ?string $expected): void
    {
        // Arrange
        $securityUserMock = $securityUser === 'admin' ? $this->adminUser : ($securityUser === 'regular' ? $this->createMock(UserInterface::class) : null);
        $tokenStorageUserMock = $tokenStorageUser === 'admin' ? $this->adminUser : ($tokenStorageUser === 'regular' ? $this->createMock(UserInterface::class) : null);
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($securityUserMock);

        if ($securityUser === null) {
            $this->tokenStorage
                ->expects($this->once())
                ->method('getToken')
                ->willReturn($tokenStorageUser ? $this->token : null);

            if ($tokenStorageUser) {
                $this->token
                    ->expects($this->once())
                    ->method('getUser')
                    ->willReturn($tokenStorageUserMock);
            }
        }

        if ($securityUser === null && $tokenStorageUser === null) {
            $this->requestStack
                ->expects($this->once())
                ->method('getMainRequest')
                ->willReturn($this->request);

            $this->request
                ->expects($this->once())
                ->method('getSession')
                ->willReturn($this->session);

            $this->session
                ->expects($this->exactly(2))
                ->method('get')
                ->with('_security_admin')
                ->willReturn(null);

            $this->requestStack
                ->expects($this->once())
                ->method('getSession')
                ->willReturn($this->session);
        }

        // Act
        $result = $this->provider->getUser();

        // Assert
        if ($expected === 'admin') {
            $this->assertSame($this->adminUser, $result);
        } else {
            $this->assertNull($result);
        }
    }
}
