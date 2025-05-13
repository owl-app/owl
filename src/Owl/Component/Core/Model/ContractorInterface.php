<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Contractor\Model\ContractorInterface as BaseContractorInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface ContractorInterface extends BaseContractorInterface
{
    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;
}
