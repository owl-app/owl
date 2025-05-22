<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

trait FilterDataComponentTrait
{
    use ComponentWithFormTrait;

    #[LiveProp]
    public array $activeCriteria = [];

    #[LiveProp]
    public array $availableFilters = [];
}
