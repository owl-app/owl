<?php

namespace FOS\ElasticaBundle\ElasticSearch\ResultSet\Factory;

use Elastic\Elasticsearch\Response\Elasticsearch;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\ResultSet;

interface ResultSetFactoryInterface
{
    public function buildResultSet(Elasticsearch $response, array $query): ResultSet;
}
