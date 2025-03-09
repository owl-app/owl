<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filter;

use Owl\Bridge\SyliusGrid\Filtering\FilterInterface;

final class BooleanFilter implements FilterInterface
{
    public const TRUE = 'true';

    public const FALSE = 'false';

    public function getQuery(string $name, $data, array $options): array
    {
        if (empty($data)) {
            return [];
        }

        $field = $options['field'] ?? $name;

        $data = self::TRUE === $data;

        return [
            "bool" => [
                "must" => [
                    'term' => [
                        $field => $data
                    ]
                ]
            ]
        ];
    }
}
