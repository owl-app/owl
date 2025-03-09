<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\Paginator;

use FOS\ElasticaBundle\ElasticSearch\Index;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\ResultSet;

class RawPaginatorAdapter implements PaginatorAdapterInterface
{
    /**
     * @var Index the object to search in
     */
    private $searchable;

    /**
     * @var array the query to search
     */
    private $query;

    /**
     * @var array<string, mixed> search options
     */
    private $options;

    /**
     * @var ?int the number of hits
     */
    private $totalHits;

    /**
     * @var array<string, mixed>|null for the aggregations
     */
    private $aggregations;

    /**
     * @var array<string, mixed>|null for the suggesters
     */
    private $suggests;

    /**
     * @var ?float
     */
    private $maxScore;

    /**
     * @see PaginatorAdapterInterface::__construct
     *
     */
    public function __construct(Index $searchable, array $query, array $options = [])
    {
        $this->searchable = $searchable;
        $this->query = $query;
        $this->options = $options;
    }

    public function getResults($offset, $itemCountPerPage)
    {
        return new RawPartialResults($this->getElasticaResults($offset, $itemCountPerPage));
    }

    /**
     * Returns the number of results.
     *
     * If genuineTotal is provided as true, total hits is returned from the
     * `hits.total` value from the search results instead of just returning
     * the requested size.
     *
     * {@inheritdoc}
     *
     * @param bool $genuineTotal
     */
    public function getTotalHits($genuineTotal = false)
    {
        if (!isset($this->totalHits)) {
            $maxResultWindow = (int) ($this->searchable->getSettings('max_result_window') ?? 10000);
            $this->totalHits = $this->searchable->count($this->query);

            if($maxResultWindow < $this->totalHits) {
                $this->totalHits = $maxResultWindow;
            }
        }

        return isset($this->options['size']) && !$genuineTotal
            ? \min($this->totalHits, (int) $this->options['size'])
            : $this->totalHits;
    }

    public function getAggregations()
    {
        if (!isset($this->aggregations)) {
            $this->aggregations = $this->searchable->search($this->query)->getAggregations();
        }

        return $this->aggregations;
    }

    public function getSuggests()
    {
        if (!isset($this->suggests)) {
            $this->suggests = $this->searchable->search($this->query)->getSuggests();
        }

        return $this->suggests;
    }

    /**
     * @return float
     */
    public function getMaxScore()
    {
        if (!isset($this->maxScore)) {
            $this->maxScore = $this->searchable->search($this->query)->getMaxScore();
        }

        return $this->maxScore;
    }

    /**
     * Returns the Query.
     *
     * @return array the search query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the paginated results.
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @throws \InvalidArgumentException
     *
     * @return ResultSet
     */
    protected function getElasticaResults($offset, $itemCountPerPage)
    {
        $offset = (int) $offset;
        $itemCountPerPage = (int) $itemCountPerPage;
        $size = isset($this->options['size'])
            ? (int) $this->options['size']
            : null;

        if (null !== $size && $size < $offset + $itemCountPerPage) {
            $itemCountPerPage = $size - $offset;
        }

        if ($itemCountPerPage < 1) {
            throw new \InvalidArgumentException('$itemCountPerPage must be greater than zero');
        }
        $options = ['from' => $offset, 'size' => $itemCountPerPage];
        $resultSet = $this->searchable->search($this->query, array_merge($this->options, $options));

        $this->totalHits = $resultSet->getTotalHits();
        $this->aggregations = $resultSet->getAggregations();
        $this->suggests = $resultSet->getSuggests();
        $this->maxScore = $resultSet->getMaxScore();

        return $resultSet;
    }
}
