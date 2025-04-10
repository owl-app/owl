<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

interface BuyerAwareInterface
{
    public function getBuyer(): ?BuyerInterface;

    public function setBuyer(?BuyerInterface $buyer): void;
}
