<?php

declare(strict_types=1);

namespace Owl\Component\Location\Model;

interface ProvinceCodeAwareInterface
{
    public function getProvinceCode(): ?string;

    public function setProvinceCode(?string $provinceCode): void;
}
