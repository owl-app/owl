<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filtering;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class FiltersApplicator implements FiltersApplicatorInterface
{
    private ServiceRegistryInterface $filtersRegistry;

    private FiltersCriteriaResolverInterface $criteriaResolver;

    public function __construct(
        ServiceRegistryInterface $filtersRegistry,
        FiltersCriteriaResolverInterface $criteriaResolver,
    ) {
        $this->filtersRegistry = $filtersRegistry;
        $this->criteriaResolver = $criteriaResolver;
    }

    public function apply(Grid $grid, Parameters $parameters): array
    {
        if (!$this->criteriaResolver->hasCriteria($grid, $parameters)) {
            return [];
        }

        $dataQuery = [];
        $criteria = $this->criteriaResolver->getCriteria($grid, $parameters);

        foreach ($criteria as $name => $data) {
            if (!$grid->hasFilter($name)) {
                continue;
            }

            $gridFilter = $grid->getFilter($name);

            if($this->filtersRegistry->has($gridFilter->getType())) {
                /** @var FilterInterface $filter */
                $filter = $this->filtersRegistry->get($gridFilter->getType());
                if($query = $filter->getQuery($name, $data, $gridFilter->getOptions())) {
                    $dataQuery[] = $query;
                }
            }
        }

        return ['bool' => ['must' => $dataQuery]];
    }
}
