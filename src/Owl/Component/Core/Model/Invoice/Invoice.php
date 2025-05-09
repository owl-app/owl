<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Invoice\Model\Invoice as BaseInvoice;
use Sylius\Component\Currency\Model\CurrencyInterface;

class Invoice extends BaseInvoice implements InvoiceInterface
{
    /** @var ContractorInterface */
    protected $contractor;

    protected bool $isContractorChanged = false;

    /** @var CurrencyInterface|null */
    protected $currency;

    public function getContractor(): ?ContractorInterface
    {
        return $this->contractor;
    }

    public function setContractor(?ContractorInterface $contractor): void
    {
        if (($this->contractor === null && $contractor !== null) || $this->contractor->getId() !== $contractor->getId()) {
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
