<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

interface InvoiceInterface extends BaseInvoiceInterface
{
    public function getBuyer(): BuyerInterface;

    public function setBuyer(BuyerInterface $buyer): void;
}
