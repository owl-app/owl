<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\ResourceInterface;

interface InvoiceSerieInterface extends ResourceInterface
{
    public function getNextCounter(): int;

    public function setNextCounter(int $nextCounter): void;

    public function getFormat(): ?string;

    public function setFormat(?string $format): void;

    public function getInvoiceType(): ?string;

    public function setInvoiceType(?string $invoiceType): void;

    public function getSequenceIncrement(): ?string;

    public function setSequenceIncrement(?string $sequenceIncrement): void;

    public function getIsDefault(): bool;

    public function setIsDefault(bool $isDefault): void;

    public function getSequences(): Collection;
}
