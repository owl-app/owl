<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filtering;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

interface FiltersApplicatorInterface
{
    public function apply(Grid $grid, Parameters $parameters): array;
}
