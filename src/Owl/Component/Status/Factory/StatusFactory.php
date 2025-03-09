<?php

declare(strict_types=1);

namespace Owl\Component\Status\Factory;

use Owl\Bridge\SyliusResource\Factory\Resource\ParentableFactory;
use Owl\Component\Status\Model\OwnerInterface;
use Owl\Component\Status\Model\StatusableInterface;
use Owl\Component\Status\Model\StatusInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of StatusInterface
 *
 * @extends ParentableFactory<T>
 *
 * @implements StatusFactoryInterface<T>
 */
final class StatusFactory extends ParentableFactory implements StatusFactoryInterface
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createNew(): StatusInterface
    {
        return $this->factory->createNew();
    }

    public function createForParent(string $parentName): StatusInterface
    {
        /** @var StatusableInterface $resourceParent */
        $resourceParent = $this->getResourceParents($parentName);

        /** @var StatusInterface $status */
        $status = $this->factory->createNew();

        $status->setStatusSubject($resourceParent);

        return $status;
    }

    public function createForSubjectWithOwner(string $parentName, ?OwnerInterface $owner): StatusInterface
    {
        /** @var StatusInterface $status */
        $status = $this->createForParent($parentName);
        $status->setOwner($owner);

        return $status;
    }
}
