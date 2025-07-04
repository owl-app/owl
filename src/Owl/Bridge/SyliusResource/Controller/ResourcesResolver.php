<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Controller;

use Doctrine\ORM\EntityRepository;
use Owl\Bridge\SyliusResource\Doctrine\Orm\CollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface as SyliusRepositoryInterface;

final class ResourcesResolver implements ResourcesResolverInterface
{
    public function __construct(
        private CollectionProviderInterface $collectionProvider,
    ) {
    }

    /**
     * @param EntityRepository<object>|RepositoryInterface|SyliusRepositoryInterface<object> $repository
     * @return iterable<object>
     */
    public function getResources(RequestConfiguration $requestConfiguration, $repository): iterable
    {
        $method = $requestConfiguration->getRepositoryMethod();

        if (null !== $method) {
            if (is_array($method) && 2 === count($method)) {
                $repository = $method[0];
                $method = $method[1];
            }

            $repositoryOptions = [
                'method' => $method,
                'arguments' => array_values($requestConfiguration->getRepositoryArguments()),
            ];

            return $this->collectionProvider->get($repository, null, $repositoryOptions);
        }

        $criteria = [];
        if ($requestConfiguration->isFilterable()) {
            $criteria = $requestConfiguration->getCriteria();
        }

        $sorting = [];
        if ($requestConfiguration->isSortable()) {
            $sorting = $requestConfiguration->getSorting();
        }

        return $this->collectionProvider->get($repository, $criteria, [], $sorting, $requestConfiguration->isPaginated());
    }
}