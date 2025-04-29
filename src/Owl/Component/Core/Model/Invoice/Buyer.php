<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Invoice\Model\Buyer\Buyer as BaseBuyer;
use Owl\Component\Contractor\Model\ContractorInterface;

class Buyer extends  BaseBuyer implements BuyerInterface
{
    /** @var ContractorInterface */
    protected $contractor;

    public function getContractor(): ?ContractorInterface
    {
        return $this->contractor;
    }

    public function setContractor(?ContractorInterface $contractor): void
    {
        $this->contractor = $contractor;
    }

    public function importContractorData(ContractorInterface $contractor): void
    {
        $this->setCompany($contractor->getCompanyName());
        $this->setTaxNumber($contractor->getTaxNumber());
        $this->setStreet($contractor->getStreet());
        $this->setCity($contractor->getCity());
        $this->setPostCode($contractor->getPostcode());
    }
}
