<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableTrait;

abstract class BaseInvoice implements BaseInvoiceInterface, ResourceInterface
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

    /** @var string|null */
    protected $paymentState;

    /** @var InvoiceSerieInterface */
    protected $serie;

    public function __construct()
    {
        $this->paymentState = self::PAYMENT_STATE_PENDING;
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

    public function getPaymentState(): string
    {
        return $this->paymentState;
    }

    public function setPaymentState(string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    public function getSerie(): ?InvoiceSerieInterface
    {
        return $this->serie;
    }

    public function setSerie(?InvoiceSerieInterface $serie): void
    {
        $this->serie = $serie;
    }
}
