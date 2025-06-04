<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Owl\Component\Invoice\Calculator\LineDataCalculator;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
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
    protected $quantity;

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

    /** @var InvoiceInterface|null */
    protected $invoice;

    /** @var TaxRateInterface|null */
    protected $taxRate;

    protected ?TaxRateSnapshotInterface $taxRateSnapshot = null;

    /** @var string|null */
    protected $calculateValuesFrom;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
        /**
         * It looks like Doctrine is hydrating decimal field as string, force casting to float.
         *
         * @var float|string|null $quantity
         */
        $quantity = $this->quantity;

        return is_string($quantity) ? (float) $quantity : $quantity;
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

    public function getCalculateValuesFrom(): ?string
    {
        return $this->calculateValuesFrom;
    }

    public function setCalculateValuesFrom(?string $calculateValuesFrom): void
    {
        $this->calculateValuesFrom = $calculateValuesFrom;

        $this->recalculateTotals();
    }

    public function getTotalPrice(): int
    {
        $unitPrice = $this->unitPrice ?? 0;
        $quantity = $this->getQuantity() ?? 0;

        return LineDataCalculator::calculateTotalPriceFromMinor($unitPrice, $quantity);
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

    public function getInvoice(): ?InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(?InvoiceInterface $invoice): void
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
        $calculateValuesFrom = $this->calculateValuesFrom ?? $this->invoice?->getCalculateValuesFrom();

        if (null === $calculateValuesFrom) {
            return;
        }

        $taxRateAmount = $this->getTaxRateAmount() ?? 0;
        $totalPrice = $this->getTotalPrice() ?? 0;

        switch ($calculateValuesFrom) {
            case CalculateValuesFromEnum::FROM_NET->value:
                $this->subtotal = $totalPrice;
                $this->taxTotal = LineDataCalculator::calculateTaxFromMinor($this->subtotal, $taxRateAmount);

                break;
            case CalculateValuesFromEnum::FROM_GROSS->value:
                $this->subtotal = (int) round($totalPrice / (1 + $taxRateAmount));
                $this->taxTotal = $totalPrice - $this->subtotal;

                break;
        }

        $this->total = $this->subtotal + $this->taxTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        $this->invoice->recalculateLineItemsTotals();
    }
}
