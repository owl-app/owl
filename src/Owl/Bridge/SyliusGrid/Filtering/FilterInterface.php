<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filtering;

interface FilterInterface
{
    /**
     * @param mixed $data
     */
    public function getQuery(string $name, $data, array $options): array;
}
