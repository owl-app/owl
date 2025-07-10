<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\Collection;
use Owl\Component\Invoice\Model\Buyer\BuyerAwareInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
use Owl\Component\Invoice\Model\Seller\SellerAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface InvoiceInterface extends
    ResourceInterface,
    BuyerAwareInterface,
    SellerAwareInterface,
    InvoicePaymentInterface,
    TotalizableInterface,
    TimestampableInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    public function isBuyerChanged(): bool;

    public function getSequenceNumber(): ?int;

    public function setSequenceNumber(?int $number): void;

    public function getFullNumber(): ?string;

    public function setFullNumber(?string $fullNumber): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getIssueDate(): ?\DateTimeInterface;

    public function setIssueDate(\DateTimeInterface $issueDate): void;

    public function getTransactionDate(): ?\DateTimeInterface;

    public function setTransactionDate(?\DateTimeInterface $transactionDate): void;

    public function getSerie(): ?InvoiceSerieInterface;

    public function setSerie(?InvoiceSerieInterface $serie): void;

    public function getExchangeRateSnapshot(): ?ExchangeRateSnapshotInterface;

    public function setExchangeRateSnapshot(?ExchangeRateSnapshotInterface $exchangeRateSnapshot): void;

    public function getCalculateValuesFrom(): ?string;

    public function setCalculateValuesFrom(?string $calculateValuesFrom): void;

    /**
     * @return Collection<int, LineItemInterface>
     */
    public function getLineItems(): Collection;

    public function hasLineItem(LineItemInterface $lineItem): bool;

    public function addLineItem(LineItemInterface $lineItem): void;

    public function removeLineItem(LineItemInterface $lineItem): void;

    public function clearLineItems(): void;

    public function getSubtotalConverted(): float;

    public function getTaxTotalConverted(): float;

    public function getTotalConverted(): float;

    public function recalculateLineItemsTotals(): void;
}
