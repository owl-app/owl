<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Owl\Component\Invoice\Model\InvoiceInterface;

interface SnapshotAssignerInterface
{
    public function assign(InvoiceInterface $invoice): void;
}
