<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

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

    /** @var boolean|null */
    protected $paymentMethod;

    /** @var boolean */
    protected $isPaid = false;

    /** @var InvoiceSerieInterface */
    protected $serie;

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

    public function getSerie(): ?InvoiceSerieInterface
    {
        return $this->serie;
    }

    public function setSerie(?InvoiceSerieInterface $serie): void
    {
        $this->serie = $serie;
    }
}
