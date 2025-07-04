<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Doctrine\Orm\Resource;

use Doctrine\ORM\EntityRepository;
use Owl\Bridge\SyliusResource\Doctrine\Orm\ItemProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface as SyliusRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @template T of ResourceInterface
 * @template TEntityClass of object
 */
final class SingleResourceProvider implements SingleResourceProviderInterface
{
    public function __construct(
        private ItemProviderInterface $itemProvider,
    ) {
    }

    /**
     * @param EntityRepository<TEntityClass>|RepositoryInterface|SyliusRepositoryInterface<T> $repository
     */
    public function get(
        RequestConfiguration $requestConfiguration,
        EntityRepository|RepositoryInterface|SyliusRepositoryInterface $repository
    ): ?ResourceInterface {
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

            return $this->itemProvider->get($repository, null, $repositoryOptions);
        }

        $criteria = [];
        $request = $requestConfiguration->getRequest();

        if ($request instanceof Request && $request->attributes->has('id')) {
            $criteria = ['identifier' => $request->attributes->get('id')];

            return $this->itemProvider->get($repository, $criteria);
        }

        if ($request instanceof Request && $request->attributes->has('slug')) {
            $criteria = ['slug' => $request->attributes->get('slug')];
        }

        $criteria = array_merge($criteria, $requestConfiguration->getCriteria());

        return $this->itemProvider->get($repository, $criteria);
    }
}