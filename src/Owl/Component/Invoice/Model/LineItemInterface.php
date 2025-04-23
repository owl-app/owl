<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface LineItemInterface extends ResourceInterface
{
    public const UNIT_PIECE = 'piece';

    public const UNIT_HOUR = 'hour';

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getQuantity(): ?float;

    public function setQuantity(?float $quantity): void;

    public function getUnit(): ?string;

    public function setUnit(?string $unit): void;

    public function getUnitNetPrice(): ?int;

    public function setUnitNetPrice(?int $unitNetPrice): void;

    public function getSubtotal(): ?int;

    public function setSubtotal(?int $subtotal): void;

    public function getTaxTotal(): ?int;

    public function setTaxTotal(?int $taxTotal): void;

    public function getTotal(): ?int;

    public function setTotal(?int $total): void;

    public function getInvoice(): ?BaseInvoiceInterface;

    public function setInvoice(?BaseInvoiceInterface $invoice): void;
}
