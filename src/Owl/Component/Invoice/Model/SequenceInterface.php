<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface SequenceInterface extends ResourceInterface
{
    public function getNextCounter(): int;

    public function setNextCounter(int $nextCounter): void;

    public function incrementNextCounter(): void;

    public function getYear(): int;

    public function setYear(int $year): void;

    public function getMonth(): ?int;

    public function setMonth(?int $month): void;

    public function getSerie(): InvoiceSerieInterface;

    public function setSerie(InvoiceSerieInterface $serie): void;
}
