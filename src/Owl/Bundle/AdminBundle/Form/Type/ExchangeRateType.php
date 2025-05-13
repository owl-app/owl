<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\CurrencyBundle\Form\Type\ExchangeRateType as BaseExchangeRateType;
use Symfony\Component\Form\AbstractType;

final class ExchangeRateType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'owl_admin_exchange_rate';
    }

    public function getParent(): string
    {
        return BaseExchangeRateType::class;
    }
}
