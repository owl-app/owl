<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\TimestampableTrait;

class InvoiceSerie implements InvoiceSerieInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var int */
    protected $nextCounter = 0;

    /** @var string|null */
    protected $format;

    /** @var string|null */
    protected $invoiceType;

    /** @var string|null */
    protected $sequenceIncrement;

    /** @var bool */
    protected $isDefault = false;

    /** @var Collection<array-key, SequenceInterface> */
    protected $sequences = [];

    public function __construct()
    {
        /** @var ArrayCollection<array-key, SequenceInterface> $this->sequences */
        $this->sequences = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function getNextCounter(): int
    {
        return $this->nextCounter;
    }

    public function setNextCounter(int $nextCounter): void
    {
        $this->nextCounter = $nextCounter;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getInvoiceType(): ?string
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(?string $invoiceType): void
    {
        $this->invoiceType = $invoiceType;
    }

    public function getSequenceIncrement(): ?string
    {
        return $this->sequenceIncrement;
    }

    public function setSequenceIncrement(?string $sequenceIncrement): void
    {
        $this->sequenceIncrement = $sequenceIncrement;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getSequences(): Collection
    {
        return $this->sequences;
    }
}
