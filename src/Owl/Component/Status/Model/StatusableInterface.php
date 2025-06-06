<?php

declare(strict_types=1);

namespace Owl\Component\Status\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\ResourceInterface;

interface StatusableInterface extends ResourceInterface
{
    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getName(): ?string;

    /**
     * @return Collection<array-key, StatusInterface>
     */
    public function getStatuses(): Collection;

    public function addStatus(StatusInterface $status): void;

    public function removeStatus(StatusInterface $status): void;
}
