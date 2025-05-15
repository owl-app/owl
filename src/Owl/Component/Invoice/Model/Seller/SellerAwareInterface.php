<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Seller;

interface SellerAwareInterface
{
    public function getSeller(): ?SellerInterface;

    public function setSeller(SellerInterface $seller): void;
}
