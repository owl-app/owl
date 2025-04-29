<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Invoice;

use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface as BaseBuyerInterface;

interface BuyerInterface extends BaseBuyerInterface
{
    public function getContractor(): ?ContractorInterface;

    public function setContractor(?ContractorInterface $contractor): void;

    public function importContractorData(ContractorInterface $contractor): void;
}
