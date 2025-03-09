<?php

namespace FOS\ElasticaBundle\ElasticSearch;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Http\Promise\Promise;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use FOS\ElasticaBundle\ElasticSearch\Exception\InvalidException;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\Factory\DefaultResultSetFactory;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\Factory\ResultSetFactoryInterface;
use FOS\ElasticaBundle\ElasticSearch\ResultSet\ResultSet;

class Index implements SearchableInterface
{
    public const OP_TYPE_CREATE = 'create';
    public const OP_TYPE_DELETE = 'delete';
    public const OP_TYPE_UPDATE = 'update';
    /**
     * Index name.
     *
     * @var string Index name
     */
    protected $_name;

    /**
     * Client object.
     *
     * @var Client Client object
     */
    protected $_client;

    /**
     * Creates a new index object.
     *
     * All the communication to and from an index goes of this object
     *
     * @param Client $client Client object
     * @param string $name   Index name
     */
    public function __construct(Client $client, string $name)
    {
        $this->_client = $client;
        $this->_name = $name;
    }

    /**
     * Return Index Stats.
     *
     * @return IndexStats
     */
    // public function getStats()
    // {
    //     return new IndexStats($this);
    // }

    /**
     * Return Index Recovery.
     *
     * @return IndexRecovery
     */
    // public function getRecovery()
    // {
    //     return new IndexRecovery($this);
    // }

    /**
     * Sets the mappings for the current index.
     *
	 * @throws MissingParameterException if a required parameter is missing
	 * @throws NoNodeAvailableException if all the hosts are offline
	 * @throws ClientResponseException if the status code of response is 4xx
	 * @throws ServerResponseException if the status code of response is 5xx
     * 
     * @param array $body 
     * @param array $query
     */
    public function setMapping(array $body, array $query = []): Elasticsearch|Promise
    {
        return $this->_client->indices()->putMapping(
            \array_merge(['index' => $this->getName(), 'body' => $body], $query)
        );
    }

    /**
     * Gets all mappings for the current index.
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function getMapping(): array
    {
        $response = $this->_client->indices()->getMapping(['index' => $this->getName()]);
        $data = $response->asArray();

        // Get first entry as if index is an Alias, the name of the mapping is the real name and not alias name
        $mapping = \array_shift($data);

        return $mapping['mappings'] ?? [];
    }

    /**
     * Returns the index settings object.
     */
    public function getSettings(string $setting = '', bool $includeDefaults = false): null|array|string
    {
        $queryParameters = [
            'include_defaults' => $includeDefaults,
        ];

        $requestData = $this->_client->indices()->getSettings(
            \array_merge(['index' => $this->getName()], $queryParameters)
        )->asArray();

        $data = \reset($requestData);

        if (empty($data['settings']) || empty($data['settings']['index'])) {
            throw new NotFoundHttpException('Index '.$this->getName().' not found');
        }

        $settings = $data['settings']['index'];
        $defaults = $data['defaults']['index'] ?? [];

        $settings = \array_merge($defaults, $settings);

        if (!$setting) {
            // return all array
            return $settings;
        }

        if (isset($settings[$setting])) {
            return $settings[$setting];
        }

        if (\str_contains($setting, '.')) {
            // translate old dot-notation settings to nested arrays
            $keys = \explode('.', $setting);
            foreach ($keys as $key) {
                if (isset($settings[$key])) {
                    $settings = $settings[$key];
                } else {
                    return null;
                }
            }

            return $settings;
        }

        return null;
    }

    /**
     * @param array|string $data
     *
     * @return Document
     */
    public function createDocument(string $id = '', $data = [])
    {
        return new Document($id, $data, $this);
    }

    /**
     * Uses _bulk to send documents to the server.
     *
     * @param Document[] $docs    Array of documents
     * @param array      $options Array of query params to use for query. For possible options check es api
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
     */
    public function updateDocuments(array $docs, array $options = []): Elasticsearch|Promise
    {
        $body = [];

        foreach ($docs as $document) {
            $document->setIndex($this->getName());

            $actionData = [
                self::OP_TYPE_UPDATE => $document->getOptions([
                    '_index',
                    '_id',
                    'version',
                    'version_type',
                    'routing',
                    'parent',
                    'retry_on_conflict',
                ])
            ];

            $documentData['doc'] = $document->getData();

            if ($document->getDocAsUpsert()) {
                $documentData['doc']['doc_as_upsert'] = true;
            } elseif ($document->hasUpsert()) {
                $upsert = $document->getUpsert()->getData();
    
                if (!empty($upsert)) {
                    $documentData['doc']['upsert'] = $upsert;
                }
            }

            $body = [...$body, ...[$actionData, $documentData]];

            unset($actionData, $documentData);
        }

        return $this->_client->bulk(array_merge(['body' => $body], $options));
    }

    /**
     * Update entries in the db based on a query.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-update-by-query.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function updateByQuery(array $body, array $options = []): Elasticsearch|Promise
    {
        $params = [
            'index' => $this->getName(),
            'body' => $body,
        ];

        return $this->_client->updateByQuery(\array_merge($params, $options));
    }

    /**
     * Adds the given document to the search index.
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function addDocument(Document $doc): Elasticsearch|Promise
    {
        $params = ['index' => $this->getName()];

        if (null !== $doc->getId() && '' !== $doc->getId()) {
            $params['id'] = $doc->getId();
        }

        $options = $doc->getOptions(
            [
                'consistency',
                'op_type',
                'parent',
                'percolate',
                'pipeline',
                'refresh',
                'replication',
                'retry_on_conflict',
                'routing',
                'timeout',
            ]
        );

        $params['body'] = $doc->getData();
        $params = \array_merge($params, $options);

        try {
            $response = $this->_client->index($params);
        } catch (ElasticsearchException $e) {
            throw $e;
        }

        $data = $response->asArray();
        // set autogenerated id to document
        if ($doc->isAutoPopulate()) {
            if (isset($data['_id']) && !$doc->hasId()) {
                $doc->setId($data['_id']);
            }
            $doc->setVersionParams($data);
        }

        return $response;
    }

    /**
     * Uses _bulk to send documents to the server.
     *
     * @param array|Document[] $docs    Array of documents
     * @param array            $options Array of query params to use for query. For possible options check es api
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
     */
    public function addDocuments(array $docs, array $options = []): Elasticsearch|Promise
    {
        if (!$docs) {
            throw new InvalidException('Array has to consist of at least one element');
        }

        $body = [];

        foreach ($docs as $document) {
            $document->setIndex($this->getName());

            $actionData = [
                self::OP_TYPE_CREATE => $document->getOptions([
                    '_index',
                    '_id',
                    'version',
                    'version_type',
                    'routing',
                    'parent',
                    'retry_on_conflict',
                ])
            ];

            $documentData = $document->getData();

            if ($document->getDocAsUpsert()) {
                $documentData['doc_as_upsert'] = true;
            } elseif ($document->hasUpsert()) {
                $upsert = $document->getUpsert()->getData();
    
                if (!empty($upsert)) {
                    $documentData['upsert'] = $upsert;
                }
            }

            $body = [...$body, ...[$actionData, $documentData]];
    
            unset($actionData, $documentData);
        }

        return $this->_client->bulk(array_merge(['body' => $body], $options));
    }

    /**
     * Get the document from search index.
     *
     * @param int|string $id      Document id
     * @param array      $options options for the get request
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     * @throws NotFoundHttpException
     */
    public function getDocument($id, array $options = []): Document
    {
        $params = \array_merge([
            'id' => $id,
            'index' => $this->getName(),
        ], $options);

        try {
            $response = $this->_client->get($params);
            $result = $response->asArray();

            if (isset($result['fields'])) {
                $data = $result['fields'];
            } elseif (isset($result['_source'])) {
                $data = $result['_source'];
            } else {
                $data = [];
            }

            $doc = new Document((string) $id, $data, $this->getName());
            $doc->setVersionParams($result);

            return $doc;
        } catch (ClientResponseException $e) {
            // 404 means the index alias doesn't exist which means no indexes have it.
            if (404 === $e->getResponse()->getStatusCode()) {
                throw new NotFoundHttpException('doc id '.$id.' not found');
            }
            // If we don't have a 404 then this is still unexpected so rethrow the exception.
            throw $e;
        }
    }

    /**
     * Deletes a document by its unique identifier.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-delete.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function deleteById(string $id, array $options = []): Elasticsearch|Promise
    {
        if (!\trim($id)) {
            throw new NotFoundHttpException('Doc id "'.$id.'" not found and can not be deleted');
        }

        $params = [
            'id' => \trim($id),
            'index' => $this->getName(),
        ];

        return $this->_client->delete(\array_merge($params, $options));
    }

    /**
     * Deletes documents matching the given query.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-delete-by-query.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function deleteByQuery($body, array $options = []): Elasticsearch|Promise
    {
        $params = \array_merge([
            'index' => $this->getName(),
            'body' => $body,
        ], $options);

        return $this->_client->deleteByQuery($params);
    }

    /**
     * Opens a Point-in-Time on the index.
     *
     * @see: https://www.elastic.co/guide/en/elasticsearch/reference/current/point-in-time-api.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function openPointInTime(string $keepAlive): Elasticsearch|Promise
    {
        return $this->_client->openPointInTime(['index' => $this->getName(), 'keep_alive' => $keepAlive]);
    }

    /**
     * Deletes the index.
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function delete(): Elasticsearch|Promise
    {
        return $this->_client->indices()->delete(['index' => $this->getName()]);
    }

    /**
     * Uses the "_bulk" endpoint to delete documents from the server.
     *
     * @param Document[] $docs Array of documents
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     * @throws BulkResponseException
     * @throws ClientException
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
     */
    // public function deleteDocuments(array $docs): ResponseSet
    // {
    //     foreach ($docs as $doc) {
    //         $doc->setIndex($this->getName());
    //     }

    //     return $this->_client->deleteDocuments($docs);
    // }

    /**
     * Force merges index.
     *
     * Detailed arguments can be found here in the ES documentation.
     *
     * @param array $args Additional arguments
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-forcemerge.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function forcemerge($args = []): Elasticsearch|Promise
    {
        return $this->_client->indices()->forcemerge(\array_merge(['index' => $this->getName(), $args]));
    }

    /**
     * Refreshes the index.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-refresh.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function refresh(): Elasticsearch|Promise
    {
        return $this->_client->indices()->refresh();
    }

    /**
     * Creates a new index with the given arguments.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-create-index.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function create(array $args = [], array $options = []): Elasticsearch|Promise
    {
        if ($options['recreate'] ?? false) {
            try {
                $this->delete();
            } catch (ClientResponseException $e) {
                // Index can't be deleted, because it doesn't exist
            }
        }

        unset($options['recreate']);

        $params = \array_merge([
            'index' => $this->getName(),
            'body' => $args,
        ], $options);

        return $this->_client->indices()->create($params);
    }

    /**
     * Checks if the given index exists ans is created.
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function exists(): bool
    {
        $response = $this->_client->indices()->exists(['index' => $this->getName()]);

        return 200 === $response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $query = [], ?array $options = null, ?ResultSetFactoryInterface $factory =  new DefaultResultSetFactory()): ResultSet
    {
        $params = [
            'index' => $this->getName(),
        ];

        if(count($query) > 0) {
            $params['body']['query'] = $query;
        }

        $response = $this->_client->search(array_merge($params, $options));

        return $factory->buildResultSet($response, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $query = [],  bool $fullResult = false, ?ResultSetFactoryInterface $factory =  new DefaultResultSetFactory()): int
    {
        $params = [
            'index' => $this->getName(),
            'size' => 0,
            'track_total_hits' => true,
            'search_type' => 'query_then_fetch',
        ];

        if(count($query) > 0) {
            $params['body']['query'] = $query;
        }

        $response = $this->_client->search($params);

        $resultSet = $factory->buildResultSet($response, $query);

        return $fullResult ? $resultSet : $resultSet->getTotalHits();
    }

    /**
     * Opens an index.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-open-close.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function open(): Elasticsearch|Promise
    {
        return $this->_client->indices()->open(['index' => $this->getName()]);
    }

    /**
     * Closes the index.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-open-close.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function close(): Elasticsearch|Promise
    {
        return $this->_client->indices()->close(['index' => $this->getName()]);
    }

    /**
     * Returns the index name.
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Returns index client.
     */
    public function getClient(): Client
    {
        return $this->_client;
    }

    /**
     * Adds an alias to the current index.
     *
     * @param bool $replace If set, an existing alias will be replaced
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-aliases.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function addAlias(string $name, bool $replace = false): Elasticsearch|Promise
    {
        $data = ['actions' => []];

        if ($replace) {
            $response = null;
            $indices = [];

            try {
                $response = $this->_client->indices()->getAlias(['name' => $name]);
            } catch (ClientResponseException $e) {
                // 404 means the index alias doesn't exist which means no indexes have it.
                if (404 === $e->getResponse()->getStatusCode()) {
                    $indices = [];
                }
                // If we don't have a 404 then this is still unexpected so rethrow the exception.
                throw $e;
            }

            foreach ($response->asArray() as $name => $unused) {
                $indices[] = $name;
            }
    
            foreach ($indices as $index) {
                $data['actions'][] = ['remove' => ['index' => $index, 'alias' => $name]];
            }
        }

        $data['actions'][] = ['add' => ['index' => $this->getName(), 'alias' => $name]];

        return $this->_client->indices()->updateAliases(['index' => $this->getName(), 'body' => $data]);
    }

    /**
     * Removes an alias pointing to the current index.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-aliases.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function removeAlias(string $name): Elasticsearch|Promise
    {
        return $this->_client->indices()->deleteAlias(['index' => $this->getName(), 'name' => $name]);
    }

    /**
     * Returns all index aliases.
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        $response = $this->_client->indices()->getAlias(['name' => '*']);
        $responseData = $response->asArray();

        if (!isset($responseData[$this->getName()])) {
            return [];
        }

        $data = $responseData[$this->getName()];
        if (!empty($data['aliases'])) {
            return \array_keys($data['aliases']);
        }

        return [];
    }

    /**
     * Checks if the index has the given alias.
     */
    public function hasAlias(string $name): bool
    {
        return \in_array($name, $this->getAliases(), true);
    }

    /**
     * Clears the cache of an index.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-clearcache.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function clearCache(): Elasticsearch|Promise
    {
        // TODO: add additional cache clean arguments
        return $this->_client->indices()->clearCache();
    }

    /**
     * Flushes the index to storage.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-flush.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function flush(array $options = []): Elasticsearch|Promise
    {
        return $this->_client->indices()->flush($options);
    }

    /**
     * Can be used to change settings during runtime. One example is to use it for bulk updating.
     *
     * @param array $data Data array
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-update-settings.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function setSettings(array $data): Elasticsearch|Promise
    {
        return $this->_client->indices()->putSettings(['index' => $this->getName(), 'body' => $data]);
    }

    /**
     * Run the analysis on the index.
     *
     * @param array $body request body for the `_analyze` API, see API documentation for the required properties
     * @param array $args Additional arguments
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-analyze.html
     *
     * @throws MissingParameterException if a required parameter is missing
     * @throws NoNodeAvailableException  if all the hosts are offline
     * @throws ClientResponseException   if the status code of response is 4xx
     * @throws ServerResponseException   if the status code of response is 5xx
     */
    public function analyze(array $body, $args = []): array
    {
        $params = \array_merge([
            'index' => $this->getName(),
            'body' => $body,
        ], $args);

        $response = $this->_client->indices()->analyze($params);
        $data = $response->asArray();

        // Support for "Explain" parameter, that returns a different response structure from Elastic
        // @see: https://www.elastic.co/guide/en/elasticsearch/reference/current/_explain_analyze.html
        if (isset($body['explain']) && $body['explain']) {
            return $data['detail'];
        }

        return $data['tokens'];
    }

    /**
     * Update document, using update script.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-update.html
     *
     * @param AbstractScript|Document $data    Document or Script with update data
     * @param array                   $options array of query params to use for query
     */
    // public function updateDocument($data, array $options = []): Response
    // {
    //     if (!($data instanceof Document) && !($data instanceof AbstractScript)) {
    //         throw new \InvalidArgumentException('Data should be a Document or Script');
    //     }

    //     if (!$data->hasId()) {
    //         throw new InvalidException('Document or Script id is not set');
    //     }

    //     return $this->_client->updateDocument($data->getId(), $data, $this->getName(), $options);
    // }

    /**
     * @var ?string
     */
    private $originalName;

    /**
     * Returns the original name of the index if the index has been renamed for reindexing
     * or realiasing purposes.
     */
    public function getOriginalName(): string
    {
        return $this->originalName ?? $this->_name;
    }

    /**
     * Reassign index name.
     *
     * While it's technically a regular setter for name property, it's specifically named overrideName, but not setName
     * since it's used for a very specific case and normally should not be used
     */
    public function overrideName(string $name): void
    {
        $this->originalName = $this->_name;
        $this->_name = $name;
    }
}
