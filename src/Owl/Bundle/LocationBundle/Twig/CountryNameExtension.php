<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Twig;

use Owl\Component\Location\Model\CountryInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountryNameExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('owl_country_name', [$this, 'translateCountryIsoCode']),
        ];
    }

    /**
     * @param CountryInterface|string|null $country
     */
    public function translateCountryIsoCode($country, ?string $locale = null): string
    {
        $countryCode = $country instanceof CountryInterface ? $country->getCode() : $country;

        if (null === $countryCode) {
            return '';
        }

        try {
            $countryName = Countries::getName($countryCode, $locale);
        } catch (MissingResourceException) {
            return $countryCode;
        }

        return $countryName;
    }
}