<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Controller\Action;

use Doctrine\ORM\EntityRepository;
use Owl\Bridge\SyliusResource\Doctrine\Orm\ItemProviderInterface;
use Owl\Bundle\AdminBundle\Controller\Action\ListResourceWithParentAction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Owl\Bridge\SyliusResource\Controller\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Owl\Bridge\SyliusResource\Controller\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

final class ListResourceWithParentActionTest extends TestCase
{
    private ListResourceWithParentAction $action;
    private string $parentName;
    private EntityRepository&MockObject $repositoryParent;
    private ResourcesCollectionProviderInterface&MockObject $resourcesCollectionProvider;
    private ItemProviderInterface&MockObject $itemProvider;
    private RequestConfigurationFactoryInterface&MockObject $requestConfigurationFactory;
    private RepositoryInterface&MockObject $repository;
    private MetadataInterface&MockObject $metadata;
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private FactoryInterface&MockObject $factory;
    private Environment&MockObject $twig;
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->parentName = 'parent';
        $this->repositoryParent = $this->createMock(EntityRepository::class);
        $this->resourcesCollectionProvider = $this->createMock(ResourcesCollectionProviderInterface::class);
        $this->itemProvider = $this->createMock(ItemProviderInterface::class);
        $this->requestConfigurationFactory = $this->createMock(RequestConfigurationFactoryInterface::class);
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->container = $this->createMock(ContainerInterface::class);

        $this->action = new ListResourceWithParentAction(
            $this->parentName,
            $this->repositoryParent,
            $this->resourcesCollectionProvider,
            $this->itemProvider,
            $this->requestConfigurationFactory,
        );

        $this->action->setRepository($this->repository);
        $this->action->setMetadata($this->metadata);
        $this->action->setAuthorizationChecker($this->authorizationChecker);
        $this->action->setEventDispatcher($this->eventDispatcher);
        $this->action->setFactory($this->factory);
        $this->action->setContainer($this->container);
    }

    public function testSuccessfullyListsResourcesWithParent(): void
    {
        // Arrange
        $parentId = 123;
        $templateName = 'index.html.twig';
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $resources = [$this->createMock(ResourceInterface::class)];
        $pluralName = 'resources';

        $request = $this->createRequestWithId($parentId);

        $this->container->method('has')
            ->willReturnCallback(function ($arg) {
                return match ($arg) {
                    'templating' => false,
                    'twig' => true,
                    default => false,
                };
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twig);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(false);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willReturn($resources);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchMultiple')
            ->with(ResourceActions::INDEX, $configuration, $resources);

        $configuration->expects($this->once())
            ->method('getTemplate')
            ->with(ResourceActions::INDEX . '.html')
            ->willReturn($templateName);

        $this->metadata->expects($this->once())
            ->method('getPluralName')
            ->willReturn($pluralName);

        $this->twig->expects($this->once())
            ->method('render')
            ->with($templateName, [
                $this->parentName => $parentResource,
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resources' => $resources,
                $pluralName => $resources,
            ])
            ->willReturn('rendered content');

        // Act
        $result = $this->action->__invoke($request);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame('rendered content', $result->getContent());
    }

    public function testThrowsAccessDeniedExceptionWhenNotAuthorized(): void
    {
        // Arrange
        $parentId = 123;
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);

        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(true);

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->with($configuration, $parentResource)
            ->willReturn(false);

        $this->expectException(AccessDeniedException::class);

        // Act
        $this->action->__invoke($request);
    }

    public function testThrowsNotFoundExceptionWhenParentResourceNotFound(): void
    {
        // Arrange
        $parentId = 999;
        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The parent has not been found');

        // Act
        $this->action->__invoke($request);
    }

    public function testThrowsNotFoundExceptionWhenIdAttributeIsMissing(): void
    {
        // Arrange
        $request = new Request();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The parent has not been found');

        // Act
        $this->action->__invoke($request);
    }

    #[DataProvider('invalidIdDataProvider')]
    public function testHandlesVariousInvalidIdScenarios(
        mixed $id,
        string $expectedMessage
    ): void {
        // Arrange
        $request = $this->createRequestWithId($id);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $id])
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage($expectedMessage);

        // Act
        $this->action->__invoke($request);
    }

    public function testHandlesItemProviderException(): void
    {
        // Arrange
        $parentId = 123;
        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willThrowException(new \RuntimeException('Item provider error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Item provider error');

        // Act
        $this->action->__invoke($request);
    }

    public function testHandlesRequestConfigurationFactoryException(): void
    {
        // Arrange
        $parentId = 123;
        $parentResource = $this->createMock(ResourceInterface::class);
        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->never())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willThrowException(new \RuntimeException('Configuration factory error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Configuration factory error');

        // Act
        $this->action->__invoke($request);
    }

    public function testHandlesResourcesCollectionProviderException(): void
    {
        // Arrange
        $parentId = 123;
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(false);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willThrowException(new \RuntimeException('Collection provider error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Collection provider error');

        // Act
        $this->action->__invoke($request);
    }

    public function testHandlesEventDispatcherException(): void
    {
        // Arrange
        $parentId = 123;
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $resources = [$this->createMock(ResourceInterface::class)];
        $request = $this->createRequestWithId($parentId);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(false);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willReturn($resources);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchMultiple')
            ->with(ResourceActions::INDEX, $configuration, $resources)
            ->willThrowException(new \RuntimeException('Event dispatcher error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Event dispatcher error');

        // Act
        $this->action->__invoke($request);
    }

    public function testHandlesTwigRenderingException(): void
    {
        // Arrange
        $parentId = 123;
        $templateName = 'index.html.twig';
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $resources = [$this->createMock(ResourceInterface::class)];
        $pluralName = 'resources';
        $request = $this->createRequestWithId($parentId);

        $this->container->method('has')
            ->willReturnCallback(function ($arg) {
                return match ($arg) {
                    'templating' => false,
                    'twig' => true,
                    default => false,
                };
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twig);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(false);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willReturn($resources);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchMultiple')
            ->with(ResourceActions::INDEX, $configuration, $resources);

        $configuration->expects($this->once())
            ->method('getTemplate')
            ->with(ResourceActions::INDEX . '.html')
            ->willReturn($templateName);

        $this->metadata->expects($this->once())
            ->method('getPluralName')
            ->willReturn($pluralName);

        $this->twig->expects($this->once())
            ->method('render')
            ->willThrowException(new \RuntimeException('Twig rendering error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Twig rendering error');

        // Act
        $this->action->__invoke($request);
    }

    public function testWorksWithAuthorizedAccess(): void
    {
        // Arrange
        $parentId = 123;
        $templateName = 'index.html.twig';
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $resources = [$this->createMock(ResourceInterface::class)];
        $pluralName = 'resources';
        $request = $this->createRequestWithId($parentId);

        $this->container->method('has')
            ->willReturnCallback(function ($arg) {
                return match ($arg) {
                    'templating' => false,
                    'twig' => true,
                    default => false,
                };
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twig);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(true);

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->with($configuration, $parentResource)
            ->willReturn(true);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willReturn($resources);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchMultiple')
            ->with(ResourceActions::INDEX, $configuration, $resources);

        $configuration->expects($this->once())
            ->method('getTemplate')
            ->with(ResourceActions::INDEX . '.html')
            ->willReturn($templateName);

        $this->metadata->expects($this->once())
            ->method('getPluralName')
            ->willReturn($pluralName);

        $this->twig->expects($this->once())
            ->method('render')
            ->with($templateName, [
                $this->parentName => $parentResource,
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resources' => $resources,
                $pluralName => $resources,
            ])
            ->willReturn('authorized content');

        // Act
        $result = $this->action->__invoke($request);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame('authorized content', $result->getContent());
    }

    #[DataProvider('emptyResourcesDataProvider')]
    public function testHandlesEmptyResourcesCollection(
        array $resources,
        string $expectedContent
    ): void {
        // Arrange
        $parentId = 123;
        $templateName = 'index.html.twig';
        $parentResource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $pluralName = 'resources';
        $request = $this->createRequestWithId($parentId);

        $this->container->method('has')
            ->willReturnCallback(function ($arg) {
                return match ($arg) {
                    'templating' => false,
                    'twig' => true,
                    default => false,
                };
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with('twig')
            ->willReturn($this->twig);

        $this->itemProvider->expects($this->once())
            ->method('get')
            ->with($this->repositoryParent, ['id' => $parentId])
            ->willReturn($parentResource);

        $this->requestConfigurationFactory->expects($this->once())
            ->method('create')
            ->with($this->metadata, $request)
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('hasPermission')
            ->willReturn(false);

        $this->resourcesCollectionProvider->expects($this->once())
            ->method('get')
            ->with($configuration, $this->repository)
            ->willReturn($resources);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchMultiple')
            ->with(ResourceActions::INDEX, $configuration, $resources);

        $configuration->expects($this->once())
            ->method('getTemplate')
            ->with(ResourceActions::INDEX . '.html')
            ->willReturn($templateName);

        $this->metadata->expects($this->once())
            ->method('getPluralName')
            ->willReturn($pluralName);

        $this->twig->expects($this->once())
            ->method('render')
            ->with($templateName, [
                $this->parentName => $parentResource,
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resources' => $resources,
                $pluralName => $resources,
            ])
            ->willReturn($expectedContent);

        // Act
        $result = $this->action->__invoke($request);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($expectedContent, $result->getContent());
    }

    private function createRequestWithId(mixed $id): Request
    {
        $request = new Request();
        if ($id !== null) {
            $request->attributes->set('id', $id);
        }

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
                'expectedMessage' => 'The parent has not been found',
            ],
            'negative id' => [
                'id' => -1,
                'expectedMessage' => 'The parent has not been found',
            ],
            'string id' => [
                'id' => 'invalid',
                'expectedMessage' => 'The parent has not been found',
            ],
            'null id' => [
                'id' => null,
                'expectedMessage' => 'The parent has not been found',
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function emptyResourcesDataProvider(): array
    {
        return [
            'empty array' => [
                'resources' => [],
                'expectedContent' => 'no resources found',
            ],
            'array with null' => [
                'resources' => [null],
                'expectedContent' => 'invalid resources',
            ],
        ];
    }
}
