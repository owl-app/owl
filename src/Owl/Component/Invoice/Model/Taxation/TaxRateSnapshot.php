<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Taxation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Resource\Model\TimestampableTrait;

class TaxRateSnapshot implements TaxRateSnapshotInterface
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

    private bool $isNameChanged = false;

    private bool $isAmountChanged = false;

    public function getId()
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
        if ($this->name !== $name) {
            $this->isNameChanged = true;
        }

        $this->name = $name;
    }

    public function isNameChanged(): bool
    {
        return $this->isNameChanged;
    }

    public function getAmount(): ?float
    {
        return $this->amount ? (float) $this->amount : null;
    }

    public function getAmountAsPercentage(): float
    {
        return $this->amount * 100;
    }

    public function isAmountChanged(): bool
    {
        return $this->isAmountChanged;
    }

    public function setAmount(?float $amount): void
    {
        if ($this->getAmount() !== $amount) {
            $this->isAmountChanged = true;
        }

        $this->amount = $amount;
    }

    public function getLabel(): ?string
    {
        return sprintf('%s (%s%%)', $this->name, $this->getAmountAsPercentage());
    }
}
