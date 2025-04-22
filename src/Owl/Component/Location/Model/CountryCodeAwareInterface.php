<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

interface CountryCodeAwareInterface
{
    public function getCountryCode(): ?string;

    public function setCountryCode(?string $countryCode): void;
}
