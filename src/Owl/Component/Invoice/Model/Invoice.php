<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableTrait;

class Invoice implements InvoiceInterface, ResourceInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $number;

    /** @var \DateTimeInterface|null */
    protected $issueDate;

    /** @var \DateTimeInterface|null */
    protected $transactionDate;

    /** @var \DateTimeInterface|null */
    protected $paymentDate;

    /** @var string|null */
    protected $paymentState;

    /** @var BuyerInterface|null */
    protected BuyerInterface $buyer;

    public function __construct()
    {
        $this->paymentState = self::PAYMENT_STATE_PENDING;
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getIssueDate(): \DateTimeInterface
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

    public function getPaymentDate(): \DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getPaymentState(): string
    {
        return $this->paymentState;
    }

    public function setPaymentState(string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    public function getBuyer(): BuyerInterface
    {
        return $this->buyer;
    }

    public function setBuyer(BuyerInterface $buyer): void
    {
        $this->buyer = $buyer;
    }
}
