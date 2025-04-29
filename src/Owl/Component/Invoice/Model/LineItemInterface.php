<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface LineItemInterface extends ResourceInterface, TotalizableInterface, TimestampableInterface
{
    public const UNIT_PIECE = 'piece';

    public const UNIT_HOUR = 'hour';

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getQuantity(): ?float;

    public function setQuantity(?float $quantity): void;

    public function getUnit(): ?string;

    public function setUnit(?string $unit): void;

    public function getUnitPrice(): ?int;

    public function setUnitPrice(?int $unitPrice): void;

    public function getCalculateValuesFrom(): ?string;

    public function setCalculateValuesFrom(?string $calculateValuesFrom): void;

    public function getInvoice(): ?BaseInvoiceInterface;

    public function setInvoice(?BaseInvoiceInterface $invoice): void;

    public function getTaxRate(): ?TaxRateInterface;

    public function getTaxRateAmount(): ?float;

    public function isTaxRateNameDiffrent(): bool;

    public function isTaxRateAmountDiffrent(): bool;

    public function setTaxRate(?TaxRateInterface $taxRate): void;

    public function getTaxRateSnapshot(): ?TaxRateSnapshotInterface;

    public function setTaxRateSnapshot(?TaxRateSnapshotInterface $taxRateSnapshot): void;
}
