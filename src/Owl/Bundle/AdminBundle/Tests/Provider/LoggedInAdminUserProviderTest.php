<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Provider;

use Owl\Bundle\AdminBundle\Provider\LoggedInAdminUserProvider;
use Owl\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class LoggedInAdminUserProviderTest extends TestCase
{
    private MockObject&Security $security;

    private MockObject&TokenStorageInterface $tokenStorage;

    private MockObject&RequestStack $requestStack;

    private MockObject&UserRepositoryInterface $adminUserRepository;

    private LoggedInAdminUserProvider $loggedInAdminUserProvider;

    private const SECURITY_SESSION_KEY = '_security_admin';

    private const SERIALIZED_TOKEN = 'O:74:"Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken":3:{i:0;N;i:1;s:4:"main";i:2;a:5:{i:0;C:34:"Owl\Component\Core\Model\AdminUser":50:{a:6:{i:0;N;i:1;N;i:2;b:0;i:3;b:0;i:4;i:404;i:5;N;}}i:1;b:1;i:2;N;i:3;a:0:{}i:4;a:1:{i:0;s:5:"admin";}}}';

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->adminUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->loggedInAdminUserProvider = new LoggedInAdminUserProvider(
            $this->security,
            $this->tokenStorage,
            $this->requestStack,
            $this->adminUserRepository,
        );
    }

    public function testImplementsLoggedInAdminUserProvider(): void
    {
        $this->assertInstanceOf(LoggedInAdminUserProviderInterface::class, $this->loggedInAdminUserProvider);
    }

    public function testReturnsTrueWhenUserIsInSecurity(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn($adminUser);

        $this->tokenStorage->expects($this->never())->method('getToken');
        $this->requestStack->expects($this->never())->method('getMainRequest');
        $this->requestStack->expects($this->never())->method('getSession');

        $this->assertTrue($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsTrueWhenUserIsInTokenStorage(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);

        $token->expects($this->once())->method('getUser')->willReturn($adminUser);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);

        $this->requestStack->expects($this->never())->method('getMainRequest');
        $this->requestStack->expects($this->never())->method('getSession');

        $this->assertTrue($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsTrueWhenUserIsInMainRequestSessionToken(): void
    {
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);

        $token->expects($this->once())->method('getUser')->willReturn(null);

        $session->expects($this->once())
            ->method('get')
            ->with(self::SECURITY_SESSION_KEY)
            ->willReturn('serialized_token')
        ;

        $request->expects($this->once())->method('getSession')->willReturn($session);

        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn($request);
        $this->requestStack->expects($this->never())->method('getSession');

        $this->assertTrue($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsTrueWhenUserIsInCurrentRequestSessionToken(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);

        $token->expects($this->once())->method('getUser')->willReturn(null);

        $session->expects($this->once())
            ->method('get')
            ->with(self::SECURITY_SESSION_KEY)
            ->willReturn('serialized_token')
        ;

        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($session);

        $this->assertTrue($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsFalseWhenThereIsNoUser(): void
    {
        $sessionMock = $this->createMock(Session::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);

        $sessionMock->expects($this->once())
            ->method('get')
            ->with(self::SECURITY_SESSION_KEY)
            ->willReturn(null)
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($sessionMock);

        $this->assertFalse($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsFalseWhenUserCannotBeProvidedAndSessionIsNotAvailableInCurrentRequest(): void
    {
        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);

        $this->requestStack->expects($this->once())
            ->method('getSession')
            ->willThrowException(new SessionNotFoundException())
        ;

        $this->assertFalse($this->loggedInAdminUserProvider->hasUser());
    }

    public function testReturnsFalseWhenUserCannotBeProvidedAndSessionIsNotAvailableInMainRequest(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn($requestMock);

        $requestMock->expects($this->once())
            ->method('getSession')
            ->willThrowException(new SessionNotFoundException())
        ;

        $this->assertFalse($this->loggedInAdminUserProvider->hasUser());
    }

    public function testGetsUserFromSecurity(): void
    {
        $adminUserMock = $this->createMock(AdminUserInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn($adminUserMock);

        $this->tokenStorage->expects($this->never())->method('getToken');
        $this->requestStack->expects($this->never())->method('getMainRequest');
        $this->requestStack->expects($this->never())->method('getSession');
        $this->adminUserRepository->expects($this->never())->method('find');

        $this->assertSame($adminUserMock, $this->loggedInAdminUserProvider->getUser());
    }

    public function testGetsUserFromTokenStorage(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->security->expects($this->once())->method('getUser')->willReturn(null);

        $token->expects($this->once())->method('getUser')->willReturn($adminUser);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);

        $this->requestStack->expects($this->never())->method('getMainRequest');
        $this->requestStack->expects($this->never())->method('getSession');
        $this->adminUserRepository->expects($this->never())->method('find');

        $this->assertSame($adminUser, $this->loggedInAdminUserProvider->getUser());
    }

    public function testGetsUserFromMainRequestSession(): void
    {
        $request = $this->createMock(Request::class);
        $session = $this->createMock(Session::class);
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->security->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn(null);

        $session
            ->expects($this->once())
            ->method('get')
            ->with(self::SECURITY_SESSION_KEY)
            ->willReturn(self::SERIALIZED_TOKEN)
        ;

        $request->method('getSession')->willReturn($session);

        $this->requestStack->method('getMainRequest')->willReturn($request);

        $adminUser->method('getId')->willReturn(404);

        $this->adminUserRepository
            ->expects($this->once())
            ->method('find')
            ->with(404)
            ->willReturn($adminUser)
        ;

        $result = $this->loggedInAdminUserProvider->getUser();

        $this->assertSame($adminUser, $result);
    }

    public function testGetsUserFromCurrentRequestSession(): void
    {
        $session = $this->createMock(Session::class);
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->security->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn(null);
        $this->requestStack->method('getMainRequest')->willReturn(null);

        $this->requestStack->method('getSession')->willReturn($session);

        $session
            ->expects($this->once())
            ->method('get')
            ->with(self::SECURITY_SESSION_KEY)
            ->willReturn(self::SERIALIZED_TOKEN)
        ;

        $adminUser->method('getId')->willReturn(404);

        $this->adminUserRepository
            ->expects($this->once())
            ->method('find')
            ->with(404)
            ->willReturn($adminUser)
        ;

        $result = $this->loggedInAdminUserProvider->getUser();

        $this->assertSame($adminUser, $result);
    }

    public function testReturnsNullWhenUserCannotBeProvided(): void
    {
        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);

        $this->requestStack->expects($this->once())
            ->method('getSession')
            ->willThrowException(new SessionNotFoundException())
        ;

        $this->adminUserRepository->expects($this->never())->method('find');

        $this->assertNull($this->loggedInAdminUserProvider->getUser());
    }
}
