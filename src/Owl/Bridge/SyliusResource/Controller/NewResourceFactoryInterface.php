<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Controller;

use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface as SyliusNewResourceFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Model\ResourceInterface;

interface NewResourceFactoryInterface extends SyliusNewResourceFactoryInterface
{
    /**
     * @template T of ResourceInterface
     * @param FactoryInterface<T> $factory
     * @param array<string, mixed> $resourceParents
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory, array $resourceParents = []): ResourceInterface;
}
