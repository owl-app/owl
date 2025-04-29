<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Owl\Component\Invoice\Calculator\LineDataCalculator;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Resource\Model\TimestampableTrait;

class LineItem implements LineItemInterface
{
    use TimestampableTrait;

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

    /** @var int|null */
    protected $unitPriceGross = 0;

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

    protected ?TaxRateSnapshotInterface $taxRateSnapshot = null;

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

        $this->recalculateTotals();
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

    public function setUnitPriceGross(?int $unitPriceGross): void
    {
        $this->unitPriceGross = $unitPriceGross;

        if (null === $unitPriceGross) {
            return;
        }

        $taxRateAmount = $this->getTaxRateAmount() ?? 0;

        $this->unitPrice = (int) round($unitPriceGross / (1 + $taxRateAmount));

        $this->recalculateTotals();
    }

    public function getUnitPriceGross(): ?int
    {
        return $this->unitPriceGross;
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

    public function getTaxRateAmount(): ?float
    {
        if (null === $this->getTaxRateSnapshot()) {
            return $this->taxRate?->getAmount();
        }

        if ($this->getTaxRateSnapshot()->getCode() === $this->getTaxRate()?->getCode()) {
            return $this->getTaxRateSnapshot()?->getAmount();
        }

        return $this->getTaxRate()?->getAmount();
    }

    public function isTaxRateNameDiffrent(): bool
    {
        if (null === $this->getTaxRateSnapshot()) {
            return false;
        }

        if ($this->getTaxRateSnapshot()->getName() === $this->getTaxRate()?->getName()) {
            return false;
        }

        return true;
    }

    public function isTaxRateAmountDiffrent(): bool
    {
        if (null === $this->getTaxRateSnapshot()) {
            return false;
        }

        $taxRateAmountSnapshot = $this->getTaxRateSnapshot()->getAmount();
        $taxRateAmount = $this->getTaxRate()?->getAmount();

        if ($this->getTaxRateSnapshot()->getAmount() !== $this->getTaxRate()?->getAmount()) {
            return true;
        }

        return false;
    }

    public function setTaxRate(?TaxRateInterface $taxRate): void
    {
        $this->taxRate = $taxRate;

        $this->recalculateTotals();
    }

    public function getTaxRateSnapshot(): ?TaxRateSnapshotInterface
    {
        return $this->taxRateSnapshot;
    }

    public function setTaxRateSnapshot(?TaxRateSnapshotInterface $taxRateSnapshot): void
    {
        $this->taxRateSnapshot = $taxRateSnapshot;

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
        $taxRateAmount = $this->getTaxRateAmount() ?? 0;

        $this->subtotal = LineDataCalculator::calculateByUnitPriceFromMinor($unitPrice, $quantity);

        if ($this->unitPriceGross > 0) {
            $this->taxTotal = LineDataCalculator::calculateByUnitPriceFromMinor($this->unitPriceGross, $quantity) - $this->subtotal;
        } else {
            $this->taxTotal = LineDataCalculator::calculateTaxFromMinor($this->subtotal, $taxRateAmount);
        }

        $this->total = $this->subtotal + $this->taxTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        if (null !== $this->invoice) {
            $this->invoice->recalculateLineItemsTotals();
        }
    }
}
