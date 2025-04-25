<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

interface TotalizableInterface
{
    public function getSubtotal(): int;

    public function getTaxTotal(): int;

    public function getTotal(): int;
}
