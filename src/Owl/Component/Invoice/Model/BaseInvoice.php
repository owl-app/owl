<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\TimestampableTrait;

abstract class BaseInvoice implements BaseInvoiceInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

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

    /** @var Collection<array-key, LineItemInterface> */
    protected $lineItems;

    /** @var int */
    protected $subtotal = 0;

    /** @var int */
    protected $taxTotal = 0;

    /** @var int */
    protected $total = 0;

    /** @var string|null */
    protected $calculateValuesFrom;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        /** @var ArrayCollection<array-key, LineItemInterface> $this->lineItems */
        $this->lineItems = new ArrayCollection();
    }

    public function getId(): string|int|null
    {
        return $this->id;
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
    }

    public function getSerie(): ?InvoiceSerieInterface
    {
        return $this->serie;
    }

    public function setSerie(?InvoiceSerieInterface $serie): void
    {
        $this->serie = $serie;
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

        $this->recalculateLineItemsTotals();
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
