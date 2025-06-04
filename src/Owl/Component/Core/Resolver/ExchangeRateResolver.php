<?php

declare(strict_types=1);

namespace Owl\Component\Core\Resolver;

use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class ExchangeRateResolver implements ExchangeRateResolverInterface
{
    public function __construct(private ExchangeRateRepositoryInterface $exchangeRateRepository)
    {
    }

    public function getRatio(string $currencyCode, string $exchangeRateCurrency): float
    {
        $exchangeRate = $this->exchangeRateRepository->findOneWithCurrencyPair($currencyCode, $exchangeRateCurrency);

        if ($exchangeRate) {
            if ($exchangeRate->getSourceCurrency()->getCode() === $currencyCode) {
                return 1 / $exchangeRate->getRatio();
            }

            return $exchangeRate->getRatio();
        }

        return 0;
    }
}
