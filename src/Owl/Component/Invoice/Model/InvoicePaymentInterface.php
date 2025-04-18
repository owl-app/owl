<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

interface InvoicePaymentInterface
{
    public function getPaymentMethod(): ?string;

    public function setPaymentMethod(?string $method): void;

    public function getDuePaymentDate(): \DateTimeInterface;

    public function setDuePaymentDate(?\DateTimeInterface $duePymentDate): void;

    public function getPaymentDate(): ?\DateTimeInterface;

    public function setPaymentDate(?\DateTimeInterface $pymentDate): void;

    public function isPaid(): bool;

    public function setIsPaid(bool $isPaid): void;
}
