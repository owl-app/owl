<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Invoice\Converter\CurrencyConverter;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use Sylius\Resource\Model\TimestampableTrait;

class Invoice implements InvoiceInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    protected ?SellerInterface $seller;

    private bool $isSellerChanged = false;

    protected ?BuyerInterface $buyer;

    private bool $isBuyerChanged = false;

    /** @var int|null */
    protected $sequenceNumber;

    /** @var string|null */
    protected $fullNumber;

    /** @var string|null */
    protected $type;

    /** @var \DateTimeInterface|null */
    protected $issueDate;

    /** @var \DateTimeInterface|null */
    protected $transactionDate;

    /** @var \DateTimeInterface|null */
    protected $duePaymentDate;

    /** @var \DateTimeInterface|null */
    protected $paymentDate;

    /** @var string|null */
    protected $paymentMethod;

    /** @var boolean */
    protected $isPaid = false;

    /** @var InvoiceSerieInterface */
    protected $serie;

    protected ?ExchangeRateSnapshotInterface $exchangeRateSnapshot = null;

    /** @var Collection<array-key, LineItemInterface> */
    protected $lineItems;

    /** @var int */
    protected $subtotal = 0;

    /** @var int */
    protected $taxTotal = 0;

    /** @var int */
    protected $total = 0;

    /** @var string|null */
    protected $calculateValuesFrom = CalculateValuesFromEnum::FROM_NET->value;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->buyer = null;
        $this->seller = null;
        $this->exchangeRateSnapshot = null;

        /** @var ArrayCollection<array-key, LineItemInterface> $this->lineItems */
        $this->lineItems = new ArrayCollection();
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function getSeller(): ?SellerInterface
    {
        return $this->seller;
    }

    public function setSeller(SellerInterface $seller): void
    {
        if ($this->seller === null || $this->seller->getId() !== $seller->getId()) {
            $this->isSellerChanged = true;
        }

        $this->seller = $seller;
    }

    public function isSellerChanged(): bool
    {
        return $this->isSellerChanged;
    }

    public function getBuyer(): ?BuyerInterface
    {
        return $this->buyer;
    }

    public function setBuyer(BuyerInterface $buyer): void
    {
        if ($this->buyer === null || $this->buyer->getId() !== $buyer->getId()) {
            $this->isBuyerChanged = true;
        }

        $this->buyer = $buyer;
    }

    public function isBuyerChanged(): bool
    {
        return $this->isBuyerChanged;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(?int $sequenceNumber): void
    {
        $this->sequenceNumber = $sequenceNumber;
    }

    public function getFullNumber(): ?string
    {
        return $this->fullNumber;
    }

    public function setFullNumber(?string $fullNumber): void
    {
        $this->fullNumber = $fullNumber;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTimeInterface $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function getTransactionDate(): \DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(?\DateTimeInterface $transactionDate): void
    {
        $this->transactionDate = $transactionDate;
    }

    public function getDuePaymentDate(): \DateTimeInterface
    {
        return $this->duePaymentDate;
    }

    public function setDuePaymentDate(?\DateTimeInterface $duePaymentDate): void
    {
        $this->duePaymentDate = $duePaymentDate;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
    }

    public function getCalculateValuesFrom(): ?string
    {
        return $this->calculateValuesFrom;
    }

    public function setCalculateValuesFrom(?string $calculateValuesFrom): void
    {
        $this->calculateValuesFrom = $calculateValuesFrom;

        foreach ($this->lineItems as $lineItem) {
            if ($lineItem->getCalculateValuesFrom() !== $calculateValuesFrom) {
                $lineItem->setCalculateValuesFrom($calculateValuesFrom);
            }
        }
    }

    public function getSerie(): ?InvoiceSerieInterface
    {
        return $this->serie;
    }

    public function setSerie(?InvoiceSerieInterface $serie): void
    {
        $this->serie = $serie;
    }

    public function getExchangeRateSnapshot(): ?ExchangeRateSnapshotInterface
    {
        return $this->exchangeRateSnapshot;
    }

    public function setExchangeRateSnapshot(?ExchangeRateSnapshotInterface $exchangeRateSnapshot): void
    {
        $this->exchangeRateSnapshot = $exchangeRateSnapshot;
    }

    public function resolveExchangeRateRatio(): ?float
    {
        if ($this->exchangeRateSnapshot === null) {
            return 0;
        }

        return $this->exchangeRateSnapshot->getRatio();
    }

    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    public function hasLineItem(LineItemInterface $lineItem): bool
    {
        return $this->lineItems->contains($lineItem);
    }

    public function addLineItem(LineItemInterface $lineItem): void
    {
        if ($this->hasLineItem($lineItem)) {
            return;
        }

        $this->lineItems->add($lineItem);
        $lineItem->setInvoice($this);
        $lineItem->setCalculateValuesFrom($this->calculateValuesFrom);
    }

    public function removeLineItem(LineItemInterface $lineItem): void
    {
        if ($this->hasLineItem($lineItem)) {
            $this->lineItems->removeElement($lineItem);

            $this->recalculateLineItemsTotals();
        }
    }

    public function clearLineItems(): void
    {
        $this->lineItems->clear();

        $this->recalculateLineItemsTotals();
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

    public function getSubtotalConverted(): float
    {
        return CurrencyConverter::defaultRound($this->subtotal * $this->resolveExchangeRateRatio());
    }

    public function getTaxTotalConverted(): float
    {
        return CurrencyConverter::defaultRound($this->taxTotal * $this->resolveExchangeRateRatio());
    }

    public function getTotalConverted(): float
    {
        return CurrencyConverter::defaultRound($this->total * $this->resolveExchangeRateRatio());
    }

    public function recalculateLineItemsTotals(): void
    {
        $this->subtotal = 0;
        $this->taxTotal = 0;
        $this->total = 0;
        
        foreach ($this->lineItems as $lineItem) {
            $this->subtotal += $lineItem->getSubtotal();
            $this->taxTotal += $lineItem->getTaxTotal();
            $this->total += $lineItem->getTotal();
        }

        if ($this->subtotal < 0) {
            $this->subtotal = 0;
        }

        if ($this->taxTotal < 0) {
            $this->taxTotal = 0;
        }

        if ($this->total < 0) {
            $this->total = 0;
        }
    }
}
