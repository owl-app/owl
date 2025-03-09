<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Controller\Action;

use Doctrine\ORM\EntityRepository;
use Owl\Bridge\SyliusResource\Controller\AbstractResourceAction;
use Owl\Bridge\SyliusResource\Doctrine\Orm\ItemProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ListResourceWithParentAction extends AbstractResourceAction
{
    public function __construct(
        private string $parentName,
        private EntityRepository $repositoryParent,
        private ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        private ItemProviderInterface $itemProvider,
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private ViewHandlerInterface $viewHandler,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $parentResource = $this->getParentResource($request);

        $this->isGrantedOr403($configuration, $parentResource);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);

        return $this->render($configuration->getTemplate(ResourceActions::INDEX . '.html'), [
            $this->parentName => $parentResource,
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resources' => $resources,
            $this->metadata->getPluralName() => $resources,
        ]);
    }

    protected function getParentResource(Request $request): ResourceInterface
    {
        $resource = $this->itemProvider->get($this->repositoryParent, ['id' => $request->attributes->get('id')]);

        if (!$request->attributes->has('id') || null === $resource) {
            throw new NotFoundHttpException(sprintf('The %s has not been found', $this->parentName));
        }

        return $resource;
    }
}
