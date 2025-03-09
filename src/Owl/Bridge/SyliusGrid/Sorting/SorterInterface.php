<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Sorting;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

interface SorterInterface
{
    public function sort(Grid $grid, Parameters $parameters): array;
}
