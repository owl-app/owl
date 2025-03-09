<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filter;

use Owl\Bridge\SyliusGrid\Filtering\FilterInterface;

final class EntityFilter implements FilterInterface
{
    public function getQuery(string $name, $data, array $options): array
    {
        if (empty($data)) {
            return [];
        }

        $values = is_array($data) ? $data : [$data];
        $fields = $options['fields'] ?? [$name];

        foreach($fields as $field) {
            if(strpos($field, '.') === false) {
                foreach($values as $value) {
                    $terms[] = [
                        "term" => [
                            $field . ".keyword" => [
                                "value" => $value
                            ]
                        ]
                    ];
                }
            }
        }

        return [
            "bool" => [
                "should" => $terms
            ]
        ];
    }
}
