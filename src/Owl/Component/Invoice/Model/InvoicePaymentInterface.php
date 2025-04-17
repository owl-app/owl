<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

interface InvoicePaymentInterface
{
    public function getPaymentMethod(): ?string;

    public function setPaymentMethod(?string $method): void;

    public function getDuePaymentDate(): \DateTimeInterface;

    public function setDuePaymentDate(?\DateTimeInterface $duePymentDate): void;
}
