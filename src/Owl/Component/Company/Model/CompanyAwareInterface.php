<?php

declare(strict_types=1);

namespace Owl\Component\Company\Model;

interface CompanyAwareInterface
{
    public function getCompany(): ?CompanyInterface;

    public function setCompany(?CompanyInterface $company): void;
}
