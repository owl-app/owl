<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\PreReRender;

trait PeriodGridFilterComponentTrait
{
    use FilterDataComponentTrait;

    #[LiveAction]
    public function changePeriod(#[LiveArg] $field, #[LiveArg] array $period): void
    {
        $flatAvailableFilters = array_merge(...array_values($this->availableFilters));

        $filterValue = array_merge(
            array_filter($this->activeCriteria, fn ($value, $key) => in_array($key, $flatAvailableFilters), \ARRAY_FILTER_USE_BOTH),
            [$field => $period],
        );

        $this->activeCriteria = array_replace($this->activeCriteria, $filterValue);

        $this->saveUserPreference($field);
    }

    #[PreReRender(priority: 100)]
    public function preRender(): void
    {
        if (isset($this->availableFilters['period'])) {
            foreach ($this->availableFilters['period'] as $field) {
                if (!isset($this->activeCriteria[$field]) || $this->activeCriteria[$field]['type'] === PeriodTypeEnum::TYPE_ALL->value) {
                    continue;
                }

                $this->formValues[$field] = array_replace($this->activeCriteria[$field] ?? [], $this->formValues[$field] ?? []);
            }
        }
    }
}
