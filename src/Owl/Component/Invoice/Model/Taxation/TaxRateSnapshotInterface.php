<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Taxation;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;

interface TaxRateSnapshotInterface extends 
    CodeAwareInterface,
    ResourceInterface,
    TimestampableInterface
{
    public function getName(): ?string;

    public function isNameChanged(): bool;

    public function setName(?string $name): void;

    public function getAmount(): ?float;

    public function isAmountChanged(): bool;

    public function getAmountAsPercentage(): float;

    public function setAmount(?float $amount): void;

    public function getLabel(): ?string;
}
