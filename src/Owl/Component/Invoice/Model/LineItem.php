<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Webmozart\Assert\Assert;

class LineItem implements LineItemInterface
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var float|null */
    protected $quantity = 0.0;

    /** @var string|null */
    protected $unit;

    /** @var int|null */
    protected $unitNetPrice;

    /** @var int|null */
    protected $subtotal;

    /** @var int|null */
    protected $taxTotal;

    /** @var int|null */
    protected $total;

    /** @var BaseInvoiceInterface|null */
    protected $invoice;

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity ? (float) $this->quantity : null;
    }

    public function setQuantity(?float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        Assert::oneOf(
            $unit,
            [self::UNIT_PIECE, self::UNIT_HOUR],
            sprintf('Wrong variant selection method "%s" given.', $unit),
        );

        $this->unit = $unit;
    }

    public function getUnitNetPrice(): ?int
    {
        return $this->unitNetPrice;
    }

    public function setUnitNetPrice(?int $unitNetPrice): void
    {
        $this->unitNetPrice = $unitNetPrice;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    public function setSubtotal(?int $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getTaxTotal(): ?int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(?int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function getInvoice(): ?BaseInvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(?BaseInvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    public static function getUnitLabels(): array
    {
        return [
            self::UNIT_HOUR => 'owl.ui.invoice.unit.hour',
            self::UNIT_PIECE => 'owl.ui.invoice.unit.piece',
        ];
    }
}
