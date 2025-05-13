<?php
declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Contractor\Model\Contractor as BaseContractor;
use Sylius\Component\Currency\Model\CurrencyInterface;

class Contractor extends BaseContractor implements ContractorInterface
{
    /** @var CurrencyInterface|null */
    protected $currency;

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }
}
