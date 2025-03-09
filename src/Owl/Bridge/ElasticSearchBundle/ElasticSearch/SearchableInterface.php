<?php

namespace FOS\ElasticaBundle\ElasticSearch;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\Factory\ResultSetFactoryInterface;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\ResultSet;

interface SearchableInterface
{
    /**
     * Searches results for a query.
     * {
     *     "from" : 0,
     *     "size" : 10,
     *     "sort" : {
     *          "postDate" : {"order" : "desc"},
     *          "user" : { },
     *          "_score" : { }
     *      },
     *      "query" : {
     *          "term" : { "user" : "kimchy" }
     *      }
     * }
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException  if the status code of response is 4xx
     * @throws ServerResponseException  if the status code of response is 5xx
     */
    public function search(array $query = [], ?array $options = null, ?ResultSetFactoryInterface $factory = null): ResultSet;

    /**
     * Counts results for a query.
     *
     * If no query is set, matchall query is created
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException  if the status code of response is 4xx
     * @throws ServerResponseException  if the status code of response is 5xx
     *
     * @return int number of documents matching the query
     */
    public function count(array $query = [],  bool $fullResult = false, ?ResultSetFactoryInterface $factory =  new DefaultResultSetFactory()): int;
}
