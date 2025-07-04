<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model\Currency;

use Sylius\Resource\Model\TimestampableTrait;

class ExchangeRateSnapshot implements ExchangeRateSnapshotInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var float|null */
    protected $ratio;

    protected bool $isRatioChanged = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getRatio(): ?float
    {
        /**
         * It looks like Doctrine is hydrating decimal field as string, force casting to float.
         *
         * @var float|string|null $ratio
         */
        $ratio = $this->ratio;

        return is_string($ratio) ? (float) $ratio : $ratio;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setRatio(?float $ratio): void
    {
        if ($this->ratio !== $ratio) {
            $this->isRatioChanged = true;
        }

        $this->ratio = $ratio;
    }

    public function isRatioChanged(): bool
    {
        return $this->isRatioChanged;
    }
}