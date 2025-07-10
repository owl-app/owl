<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Doctrine\Orm;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ItemProviderInterface
{
    public const TYPE = 'item';

    /**
     * @param EntityRepository<ResourceInterface> $repository
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $repositoryOptions
     */
    public function get(EntityRepository $repository, ?array $criteria = [], array $repositoryOptions = []): ?ResourceInterface;
}
