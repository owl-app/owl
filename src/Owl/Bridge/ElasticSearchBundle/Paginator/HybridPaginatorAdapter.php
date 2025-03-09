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

use FOS\ElasticaBundle\ElasticSearch\SearchableInterface;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;

class HybridPaginatorAdapter extends RawPaginatorAdapter
{
    private $transformer;

    /**
     * @param SearchableInterface                 $searchable  the object to search in
     * @param array                               $query       the query to search
     * @param ElasticaToModelTransformerInterface $transformer the transformer for fetching the results
     */
    public function __construct(SearchableInterface $searchable, array $query, array $options, ElasticaToModelTransformerInterface $transformer)
    {
        parent::__construct($searchable, $query, $options);

        $this->transformer = $transformer;
    }

    public function getResults($offset, $length)
    {
        return new HybridPartialResults($this->getElasticaResults($offset, $length), $this->transformer);
    }
}
