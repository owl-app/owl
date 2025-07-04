<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Taxation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Resource\Model\TimestampableTrait;

class TaxRate implements TaxRateInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var float|null */
    protected $amount = 0.0;

    protected ?\DateTimeInterface $startDate = null;

    protected ?\DateTimeInterface $endDate = null;

    /** @var Collection<array-key, ZoneInterface> */
    protected $zones;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        /** @var ArrayCollection<array-key, ZoneInterface> $this->zones */
        $this->zones = new ArrayCollection();
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): ?float
    {
        return $this->amount ? (float) $this->amount : null;
    }

    public function getAmountAsPercentage(): float
    {
        return $this->amount * 100;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function getLabel(): ?string
    {
        return sprintf('%s (%s%%)', $this->name, $this->getAmountAsPercentage());
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Collection<array-key, ZoneInterface>
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function hasZone(ZoneInterface $zone): bool
    {
        return $this->zones->contains($zone);
    }

    public function addZone(ZoneInterface $zone): void
    {
        if (!$this->hasZone($zone)) {
            $this->zones->add($zone);
        }
    }

    public function removeZone(ZoneInterface $zone): void
    {
        if ($this->hasZone($zone)) {
            $this->zones->removeElement($zone);
        }
    }

    public function clearZones(): void
    {
        $this->zones->clear();
    }
}