<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Owl\Component\Invoice\Calculator\MoneyCalculator;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Webmozart\Assert\Assert;

class LineItem implements LineItemInterface
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var float|null */
    protected ?float $quantity;

    /** @var string|null */
    protected $unit;

    /** @var int|null */
    protected $unitPrice = 0;

    /** @var int */
    protected $subtotal = 0;

    /** @var int */
    protected $taxTotal = 0;

    /** @var int */
    protected $total = 0;

    /** @var BaseInvoiceInterface|null */
    protected $invoice;

    /** @var TaxRateInterface|null */
    protected $taxRate;

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
        // Assert::oneOf(
        //     $unit,
        //     [self::UNIT_PIECE, self::UNIT_HOUR],
        //     sprintf('Wrong variant selection method "%s" given.', $unit),
        // );

        $this->unit = $unit;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;

        $this->recalculateTotals();
    }

    public function getSubtotal(): int
    {
        return $this->subtotal;
    }

    public function getTaxTotal(): int
    {
        return $this->taxTotal;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getInvoice(): ?BaseInvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(?BaseInvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getTaxRate(): ?TaxRateInterface
    {
        return $this->taxRate;
    }

    public function setTaxRate(?TaxRateInterface $taxRate): void
    {
        $this->taxTotal = 0;
        $this->taxRate = $taxRate;

        if (null !== $this->taxRate && $this->subtotal > 0) {
            $this->taxTotal = MoneyCalculator::calculateTaxFromMinor($this->subtotal, $this->taxRate->getAmount());
        }

        $this->recalculateTotals();
    }

    public static function getUnitLabels(): array
    {
        return [
            self::UNIT_HOUR => 'owl.invoice.unit.hour',
            self::UNIT_PIECE => 'owl.invoice.unit.piece',
        ];
    }

    protected function recalculateTotals(): void
    {
        $unitPrice = $this->unitPrice ?? 0;
        $quantity = $this->quantity ?? 0;

        $this->subtotal = MoneyCalculator::calculateSubtotalFromMinor($unitPrice, $quantity);
        $this->total = $this->subtotal + $this->taxTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        if (null !== $this->invoice) {
            $this->invoice->recalculateLineItemsTotals();
        }
    }
}
