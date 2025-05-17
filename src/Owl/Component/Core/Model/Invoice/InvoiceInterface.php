<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Invoice\Model\InvoiceInterface as BaseInvoiceInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Owl\Component\Core\Model\ContractorInterface;
use Owl\Component\Core\Model\CompanyInterface;

interface InvoiceInterface extends BaseInvoiceInterface
{
    public function getCompany(): ?CompanyInterface;

    public function setCompany(?CompanyInterface $company): void;

    public function isCompanyChanged(): bool;

    public function getContractor(): ?ContractorInterface;

    public function setContractor(?ContractorInterface $contractor): void;

    public function isContractorChanged(): bool;

    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;
}
