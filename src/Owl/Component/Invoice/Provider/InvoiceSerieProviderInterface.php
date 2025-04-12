<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Provider;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;

interface InvoiceSerieProviderInterface
{
    public function getSerie(): InvoiceSerieInterface;
}
