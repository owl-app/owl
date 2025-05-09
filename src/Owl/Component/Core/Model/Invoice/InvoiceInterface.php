<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Invoice\Model\InvoiceInterface as BaseInvoiceInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Owl\Component\Contractor\Model\ContractorInterface;

interface InvoiceInterface extends BaseInvoiceInterface
{
    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;

    public function getContractor(): ?ContractorInterface;

    public function setContractor(?ContractorInterface $contractor): void;

    public function isContractorChanged(): bool;
}
