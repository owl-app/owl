<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid\GridFilterComponentTrait;
use Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid\PeriodGridFilterComponentTrait;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
class InvoiceGridFilterComponent
{
    use TemplatePropTrait;
    use PeriodGridFilterComponentTrait;
    use GridFilterComponentTrait {
        initialize as public __construct;
    }
}
