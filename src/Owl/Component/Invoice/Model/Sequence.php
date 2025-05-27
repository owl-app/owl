<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Resource\Model\TimestampableTrait;

class Sequence implements SequenceInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var int */
    protected $nextCounter = 0;

    /** @var int */
    protected $year;

    /** @var int|null */
    protected $month;

    protected int $version;

    /** @var InvoiceSerieInterface */
    protected $serie;

    public function __construct()
    {
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

    public function incrementNextCounter(): void
    {
        ++$this->nextCounter;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): void
    {
        $this->month = $month;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    public function getSerie(): InvoiceSerieInterface
    {
        return $this->serie;
    }

    public function setSerie(InvoiceSerieInterface $serie): void
    {
        $this->serie = $serie;
    }
}
