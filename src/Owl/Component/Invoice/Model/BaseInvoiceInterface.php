<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface BaseInvoiceInterface extends ResourceInterface, InvoicePaymentInterface, TotalizableInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    public function getSequenceNumber(): ?int;

    public function setSequenceNumber(?int $number): void;

    public function getFullNumber(): ?string;

    public function setFullNumber(?string $fullNumber): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getIssueDate(): ?\DateTimeInterface;

    public function setIssueDate(\DateTimeInterface $issueDate): void;

    public function getTransactionDate(): \DateTimeInterface;

    public function setTransactionDate(?\DateTimeInterface $transactionDate): void;

    public function getSerie(): ?InvoiceSerieInterface;

    public function setSerie(?InvoiceSerieInterface $serie): void;

    public function getLineItems(): Collection;

    public function hasLineItem(LineItemInterface $lineItem): bool;

    public function addLineItem(LineItemInterface $lineItem): void;

    public function removeLineItem(LineItemInterface $lineItem): void;

    public function clearLineItems(): void;

    public function getTotal(): int;

    public function recalculateLineItemsTotals(): void;
}
