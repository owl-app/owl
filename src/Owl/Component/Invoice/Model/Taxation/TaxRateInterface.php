<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Taxation;

use Doctrine\Common\Collections\Collection;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;

interface TaxRateInterface extends
    CodeAwareInterface,
    ResourceInterface,
    TimestampableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getAmount(): ?float;

    public function getAmountAsPercentage(): float;

    public function setAmount(?float $amount): void;

    public function getLabel(): ?string;

    public function getStartDate(): ?\DateTimeInterface;

    public function setStartDate(?\DateTimeInterface $startDate): void;

    public function getEndDate(): ?\DateTimeInterface;

    public function setEndDate(?\DateTimeInterface $endDate): void;

    /**
     * @return Collection<int, ZoneInterface>
     */
    public function getZones(): Collection;

    public function hasZone(ZoneInterface $zone): bool;

    public function addZone(ZoneInterface $zone): void;

    public function removeZone(ZoneInterface $zone): void;

    public function clearZones(): void;
}
