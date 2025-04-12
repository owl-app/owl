<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface BaseInvoiceInterface extends ResourceInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    public function getNumber(): string;

    public function setNumber(string $number): void;

    public function getFullNumber(): string;

    public function setFullNumber(string $fullNumber): void;

    public function getIssueDate(): \DateTimeInterface;

    public function setIssueDate(\DateTimeInterface $issueDate): void;

    public function getTransactionDate(): \DateTimeInterface;

    public function setTransactionDate(?\DateTimeInterface $transactionDate): void;

    public function getDuePaymentDate(): \DateTimeInterface;

    public function setDuePaymentDate(?\DateTimeInterface $duePymentDate): void;

    public function getPaymentState(): string;

    public function setPaymentState(string $paymentState): void;
}
