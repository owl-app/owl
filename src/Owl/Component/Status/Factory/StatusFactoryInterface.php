<?php

declare(strict_types=1);

namespace Owl\Component\Status\Factory;

use Owl\Component\Status\Model\OwnerInterface;
use Owl\Component\Status\Model\StatusInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface StatusFactoryInterface extends FactoryInterface
{
    /**
     * @param string $parentName
     * @param OwnerInterface|null $owner
     * @return StatusInterface
     */
    public function createForSubjectWithOwner(string $parentName, ?OwnerInterface $owner): StatusInterface;
}