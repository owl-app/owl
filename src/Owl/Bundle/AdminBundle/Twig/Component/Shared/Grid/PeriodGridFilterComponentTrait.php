<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Grid;

use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Symfony\UX\LiveComponent\Attribute\PreReRender;

trait PeriodGridFilterComponentTrait
{
    use FilterDataComponentTrait;

    #[PreReRender(priority: 100)]
    public function preRender(): void
    {
        if (isset($this->availableFilters['period'])) {
            foreach($this->availableFilters['period'] as $field) {
                if (!isset($this->activeCriteria[$field]) || $this->activeCriteria[$field]['type'] === PeriodTypeEnum::TYPE_ALL->value) {
                    continue;
                }

                $this->formValues[$field] = array_replace($this->activeCriteria[$field] ?? [], $this->formValues[$field] ?? []);
            }
        }
    }
}
