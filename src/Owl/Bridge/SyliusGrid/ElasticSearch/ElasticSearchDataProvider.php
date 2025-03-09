<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\ElasticSearch;

use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Owl\Bridge\SyliusGrid\Filtering\FiltersApplicatorInterface;
use Owl\Bridge\SyliusGrid\Sorting\SorterInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class ElasticSearchDataProvider implements DataProviderInterface
{
    public const DRIVER_ELASTICSEARCH = 'elasticsearch';

    public function __construct(
        private ?ServiceRegistryInterface $registryFinder = null,
        private SorterInterface $sorter,
        private FiltersApplicatorInterface $filtersApplicator
    ) {
    }

    public function getData(Grid $grid, Parameters $parameters)
    {
        $driverConfiguration = $grid->getDriverConfiguration();

        if(!isset($driverConfiguration['elasticsearch']['finder'])) {
            throw new \InvalidArgumentException('Finder not found in driver configuration');
        }

        /** @var PaginatedFinderInterface $finder */
        $finder = $this->registryFinder->get($driverConfiguration['elasticsearch']['finder']);

        $query = $this->filtersApplicator->apply($grid, $parameters);
        $options['sort'] = $this->sorter->sort($grid, $parameters);

        return $finder->findPaginated($query, $options);
    }
}
