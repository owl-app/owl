<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Controller\Action;

use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\RestBundle\View\View;
use Owl\Bundle\AdminBundle\Controller\Action\AcceptNotificationAction;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Factory\NotificationAcceptedFactoryInterface;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Owl\Component\Core\Repository\NotificationRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Owl\Bridge\SyliusResource\Controller\AuthorizationCheckerInterface;
use Owl\Bridge\SyliusResource\Controller\EventDispatcherInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[CoversClass(AcceptNotificationAction::class)]
final class AcceptNotificationActionTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    private AcceptNotificationAction $action;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private RepositoryInterface&MockObject $notificationAcceptedRepository;

    private NotificationAcceptedFactoryInterface&MockObject $notificationAcceptedFactory;

    private RequestConfigurationFactoryInterface&MockObject $requestConfigurationFactory;

    private ViewHandlerInterface&MockObject $viewHandler;

    private NotificationRepositoryInterface&MockObject $repository;

    private MetadataInterface&MockObject $metadata;

    private CsrfTokenManagerInterface&MockObject $csrfTokenManager;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private FactoryInterface&MockObject $factory;

    private AdminUserInterface&MockObject $user;

    private NotificationInterface&MockObject $notification;

    private NotificationAcceptedInterface&MockObject $notificationAccepted;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->notificationAcceptedRepository = $this->createMock(RepositoryInterface::class);
        $this->notificationAcceptedFactory = $this->createMock(NotificationAcceptedFactoryInterface::class);
        $this->requestConfigurationFactory = $this->createMock(RequestConfigurationFactoryInterface::class);
        $this->viewHandler = $this->createMock(ViewHandlerInterface::class);
        $this->repository = $this->createMock(NotificationRepositoryInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->user = $this->createMock(AdminUserInterface::class);
        $this->notification = $this->createMock(NotificationInterface::class);
        $this->notificationAccepted = $this->createMock(NotificationAcceptedInterface::class);

        $this->action = new AcceptNotificationAction(
            $this->adminUserContext,
            $this->notificationAcceptedRepository,
            $this->notificationAcceptedFactory,
            $this->requestConfigurationFactory,
            $this->viewHandler,
        );

        $this->action->setRepository($this->repository);
        $this->action->setMetadata($this->metadata);
        $this->action->setAuthorizationChecker($this->authorizationChecker);
        $this->action->setEventDispatcher($this->eventDispatcher);
        $this->action->setFactory($this->factory);

        $this->container->method('get')
            ->with('security.csrf.token_manager')
            ->willReturn($this->csrfTokenManager);
        $this->action->setContainer($this->container);
    }

    #[Test]
    public function it_successfully_accepts_notification_with_valid_csrf_token(): void
    {
        // Arrange
        $notificationId = 123;
        $csrfToken = 'valid-csrf-token';
        $user = $this->user;
        $notification = $this->notification;
        $notificationAccepted = $this->notificationAccepted;
        $configuration = $this->createMock(RequestConfiguration::class);
        $response = new Response('', Response::HTTP_CREATED);

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with($this->callback(function (CsrfToken $token) use ($notificationId, $csrfToken) {
                return $token->getId() === (string) $notificationId && $token->getValue() === $csrfToken;
            }))
            ->willReturn(true);

        $this->notificationAcceptedFactory->expects($this->once())
            ->method('createAction')
            ->with($notification, $user)
            ->willReturn($notificationAccepted);

        $this->notificationAcceptedRepository->expects($this->once())
            ->method('add')
            ->with($notificationAccepted);

        $this->viewHandler->expects($this->once())
            ->method('handle')
            ->with($configuration, $this->callback(function (View $view) use ($notification) {
                return $view->getData() === $notification && $view->getStatusCode() === Response::HTTP_CREATED;
            }))
            ->willReturn($response);

        // Act
        $result = $this->action->__invoke($request);

        // Assert
        $this->assertSame($response, $result);
    }

    #[Test]
    public function it_throws_http_exception_when_csrf_token_is_invalid(): void
    {
        // Arrange
        $notificationId = 123;
        $csrfToken = 'invalid-csrf-token';
        $user = $this->user;
        $notification = $this->notification;

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Invalid csrf token.');

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    public function it_throws_not_found_exception_when_notification_not_found(): void
    {
        // Arrange
        $notificationId = 999;
        $user = $this->user;

        $request = $this->createRequestWithCsrfToken($notificationId, 'token');

        $this->adminUserContext->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn(null);

        $this->metadata->expects($this->once())
            ->method('getHumanizedName')
            ->willReturn('notification');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The "notification" has not been found');

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    public function it_throws_not_found_exception_when_id_attribute_is_missing(): void
    {
        // Arrange
        $request = new Request();
        $request->request = new ParameterBag(['_csrf_token' => 'token']);
        $user = $this->user;

        $this->metadata->expects($this->once())
            ->method('getHumanizedName')
            ->willReturn('notification');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with(0, $user, 'admin')
            ->willReturn(null);

        $this->adminUserContext->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The "notification" has not been found');

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    #[DataProvider('invalidCsrfTokenDataProvider')]
    public function it_handles_various_invalid_csrf_token_scenarios(
        ?string $csrfToken,
        string $expectedMessage
    ): void {
        // Arrange
        $notificationId = 123;
        $user = $this->user;
        $notification = $this->notification;

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(false);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage($expectedMessage);

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    #[DataProvider('invalidIdDataProvider')]
    public function it_handles_various_invalid_id_scenarios(
        mixed $id,
        string $expectedMessage
    ): void {
        // Arrange
        $request = new Request();
        $request->attributes->set('id', $id);
        $request->request = new ParameterBag(['_csrf_token' => 'token']);

        $this->metadata->expects($this->once())
            ->method('getHumanizedName')
            ->willReturn('notification');

        $this->adminUserContext->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with(is_int($id) ? $id : 0, $this->user, 'admin')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage($expectedMessage);

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    public function it_handles_notification_accepted_factory_exception(): void
    {
        // Arrange
        $notificationId = 123;
        $csrfToken = 'valid-csrf-token';
        $user = $this->user;
        $notification = $this->notification;
        $configuration = $this->createMock(RequestConfiguration::class);

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(true);

        $this->notificationAcceptedFactory->expects($this->once())
            ->method('createAction')
            ->with($notification, $user)
            ->willThrowException(new \RuntimeException('Factory error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Factory error');

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    public function it_handles_repository_add_exception(): void
    {
        // Arrange
        $notificationId = 123;
        $csrfToken = 'valid-csrf-token';
        $user = $this->user;
        $notification = $this->notification;
        $notificationAccepted = $this->notificationAccepted;
        $configuration = $this->createMock(RequestConfiguration::class);

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(true);

        $this->notificationAcceptedFactory->expects($this->once())
            ->method('createAction')
            ->with($notification, $user)
            ->willReturn($notificationAccepted);

        $this->notificationAcceptedRepository->expects($this->once())
            ->method('add')
            ->with($notificationAccepted)
            ->willThrowException(new \RuntimeException('Repository error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Repository error');

        // Act
        $this->action->__invoke($request);
    }

    #[Test]
    public function it_handles_view_handler_exception(): void
    {
        // Arrange
        $notificationId = 123;
        $csrfToken = 'valid-csrf-token';
        $user = $this->user;
        $notification = $this->notification;
        $notificationAccepted = $this->notificationAccepted;
        $configuration = $this->createMock(RequestConfiguration::class);

        $request = $this->createRequestWithCsrfToken($notificationId, $csrfToken);

        $notification->expects($this->once())
            ->method('getId')
            ->willReturn($notificationId);

        $this->adminUserContext->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $this->adminUserContext->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('admin');

        $this->repository->expects($this->once())
            ->method('findNotAccepted')
            ->with($notificationId, $user, 'admin')
            ->willReturn($notification);

        $this->container->expects($this->once())
            ->method('has')
            ->with('security.csrf.token_manager')
            ->willReturn(true);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $this->csrfTokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(true);

        $this->notificationAcceptedFactory->expects($this->once())
            ->method('createAction')
            ->with($notification, $user)
            ->willReturn($notificationAccepted);

        $this->notificationAcceptedRepository->expects($this->once())
            ->method('add')
            ->with($notificationAccepted);

        $this->viewHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new \RuntimeException('View handler error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('View handler error');

        // Act
        $this->action->__invoke($request);
    }

    private function createRequestWithCsrfToken(int $notificationId, ?string $csrfToken): Request
    {
        $request = new Request();
        $request->attributes->set('id', $notificationId);
        $request->request = new ParameterBag(['_csrf_token' => $csrfToken]);

        return $request;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function invalidIdDataProvider(): array
    {
        return [
            'zero id' => [
                'id' => 0,
                'expectedMessage' => 'The "notification" has not been found',
            ],
            'negative id' => [
                'id' => -1,
                'expectedMessage' => 'The "notification" has not been found',
            ],
            'string id' => [
                'id' => 'invalid',
                'expectedMessage' => 'The "notification" has not been found',
            ],
            'null id' => [
                'id' => null,
                'expectedMessage' => 'The "notification" has not been found',
            ],
        ];
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    public static function invalidCsrfTokenDataProvider(): array
    {
        return [
            'empty string token' => [
                'csrfToken' => '',
                'expectedMessage' => 'Invalid csrf token.',
            ],
            'null token' => [
                'csrfToken' => null,
                'expectedMessage' => 'Invalid csrf token.',
            ],
            'wrong token' => [
                'csrfToken' => 'wrong-token',
                'expectedMessage' => 'Invalid csrf token.',
            ],
        ];
    }
}
