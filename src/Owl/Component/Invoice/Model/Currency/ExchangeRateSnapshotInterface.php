<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Currency;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;

interface ExchangeRateSnapshotInterface extends 
    CodeAwareInterface,
    ResourceInterface,
    TimestampableInterface
{
    public function getRatio(): ?float;

    public function setRatio(?float $ratio);

    public function isRatioChanged(): bool;
}
