<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Core\Model\ContractorInterface;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Invoice\Model\Invoice as BaseInvoice;
use Sylius\Component\Currency\Model\CurrencyInterface;

class Invoice extends BaseInvoice implements InvoiceInterface
{
    /** @var CompanyInterface */
    protected $company;

    /** @var ContractorInterface */
    protected $contractor;

    protected bool $isCompanyChanged = false;

    protected bool $isContractorChanged = false;

    protected ?CurrencyInterface $currency = null;

    public function getCompany(): ?CompanyInterface
    {
        return $this->company;
    }

    public function setCompany(?CompanyInterface $company): void
    {
        if (($this->company === null && $company !== null) || ($company && $this->company->getId() !== $company->getId())) {
            $this->isCompanyChanged = true;
        }

        $this->company = $company;
    }

    public function isCompanyChanged(): bool
    {

        return $this->isCompanyChanged;
    }

    public function getContractor(): ?ContractorInterface
    {
        return $this->contractor;
    }

    public function setContractor(?ContractorInterface $contractor): void
    {
        if (($this->contractor === null && $contractor !== null) || ($contractor && $this->contractor->getId() !== $contractor->getId())) {
            $this->isContractorChanged = true;
        }

        $this->contractor = $contractor;
    }

    public function isContractorChanged(): bool
    {

        return $this->isContractorChanged;
    }

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }
}
