<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Invoice\Model\BaseInvoice;

class Invoice extends BaseInvoice implements InvoiceInterface
{
    /** @var BuyerInterface */
    protected $buyer;

    public function getBuyer(): ?BuyerInterface
    {
        return $this->buyer;
    }

    public function setBuyer(?BuyerInterface $buyer): void
    {
        $this->buyer = $buyer;
    }
}
