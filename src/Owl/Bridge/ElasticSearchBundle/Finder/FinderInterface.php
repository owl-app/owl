<?php

/*
 * This file is part of the FOSElasticaBundle package.
 *
 * (c) FriendsOfSymfony <https://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\ElasticaBundle\Finder;

/**
 * @phpstan-type TQuery = array<string, mixed>
 * @phpstan-type TOptions = array<string, mixed>
 */
interface FinderInterface
{
    /**
     * Searches for query results within a given limit.
     *
     * @param mixed $query Can be a string, an array
     *
     * @phpstan-param TQuery $query
     *
     * @param int|null $limit How many results to get
     *
     * @phpstan-param TOptions $options
     *
     * @return array<object> results
     */
    public function find($query, ?int $limit = null, array $options = []);
}
