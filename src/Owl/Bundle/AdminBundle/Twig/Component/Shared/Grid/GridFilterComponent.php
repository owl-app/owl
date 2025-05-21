<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
class GridFilterComponent
{
    use TemplatePropTrait;

    use GridFilterComponentTrait {
        initialize as public __construct;
    }
}
