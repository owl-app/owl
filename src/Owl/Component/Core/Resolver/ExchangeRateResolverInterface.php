<?php

declare(strict_types=1);

namespace Owl\Component\Core\Resolver;

interface ExchangeRateResolverInterface
{
    public function getRatio(string $currencyCode, string $exchangeRateCurrency): float;
}
