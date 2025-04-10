<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Invoice\Model\BaseInvoiceInterface;

interface InvoiceInterface extends BaseInvoiceInterface, BuyerAwareInterface
{

}
