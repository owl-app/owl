<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Twig;

use Owl\Component\Location\Provider\ProvinceNamingProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ProvinceNamingExtension extends AbstractExtension
{
    public function __construct(private ProvinceNamingProviderInterface $provinceNamingProvider)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_province_name', [$this->provinceNamingProvider, 'getName']),
            new TwigFilter('sylius_province_abbreviation', [$this->provinceNamingProvider, 'getAbbreviation']),
        ];
    }
}
