<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

class Invoice extends BaseInvoice implements InvoiceInterface
{
    /** @var BuyerInterface */
    protected BuyerInterface $buyer;

    public function getBuyer(): BuyerInterface
    {
        return $this->buyer;
    }

    public function setBuyer(BuyerInterface $buyer): void
    {
        $this->buyer = $buyer;
    }
}
