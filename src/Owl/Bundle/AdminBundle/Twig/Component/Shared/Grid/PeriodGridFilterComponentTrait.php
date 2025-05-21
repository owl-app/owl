<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

trait PeriodGridFilterComponentTrait
{
    use ComponentWithFormTrait;

    #[LiveAction]
    public function updateNext(): void
    {
        $this->formValues['issueDate'] = array_merge(
            $this->formValues['issueDate'] ?? [],
            [
                'month' => 11
            ],
        );
    }
}
