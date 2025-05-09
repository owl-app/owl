<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Buyer;

interface BuyerAwareInterface
{
    public function getBuyer(): ?BuyerInterface;

    public function setBuyer(BuyerInterface $buyer): void;
}
