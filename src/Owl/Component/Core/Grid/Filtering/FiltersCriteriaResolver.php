<?php

declare(strict_types=1);

namespace Owl\Component\Core\Grid\Filtering;

use Owl\Component\Core\Manager\UserPreferenceManagerInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;

final class FiltersCriteriaResolver implements FiltersCriteriaResolverInterface
{
    public function __construct(
        private FiltersCriteriaResolverInterface $decoratedFiltersCriteriaResolver,
        private UserPreferenceManagerInterface $userPreferenceManager,
    ) {
    }

    public function hasCriteria(Grid $grid, Parameters $parameters): bool
    {
        return $this->decoratedFiltersCriteriaResolver->hasCriteria($grid, $parameters) ||
            $this->hasUserPreferences($grid)
        ;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCriteria(Grid $grid, Parameters $parameters): array
    {
        $criteria = $this->decoratedFiltersCriteriaResolver->getCriteria($grid, $parameters);
        $criteriaUserPreferences = $this->userPreferenceManager->get('filters.' . $grid->getCode()) ?? [];

        return array_merge($criteriaUserPreferences, $criteria);
    }

    private function hasUserPreferences(Grid $grid): bool
    {
        return $this->userPreferenceManager->has('filters.' . $grid->getCode());
    }
}
