<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Filter;

use Owl\Bridge\SyliusGrid\Filtering\FilterInterface;

final class StringFilter implements FilterInterface
{
    public const NAME = 'string';

    public const TYPE_EQUAL = 'equal';

    public const TYPE_NOT_EQUAL = 'not_equal';

    public const TYPE_EMPTY = 'empty';

    public const TYPE_NOT_EMPTY = 'not_empty';

    public const TYPE_CONTAINS = 'contains';

    public const TYPE_NOT_CONTAINS = 'not_contains';

    public function getQuery(string $name, $data, array $options): array
    {
        $value = is_array($data) ? $data['value'] ?? null : $data;
        $type = $data['type'] ?? ($options['type'] ?? self::TYPE_CONTAINS);
        $fields = $options['fields'] ?? [$name];

        if (!in_array($type, [self::TYPE_NOT_EMPTY, self::TYPE_EMPTY], true) && '' === trim((string) $value)) {
            return [];
        }

        if (1 === count($fields)) {
            return [
                'match' => [
                    end($fields) => $value
                ]
            ];
        }

        $nestedFields = $this->getNestedFields($fields);

        switch ($type) {
            case self::TYPE_EQUAL:
            case self::TYPE_NOT_EQUAL:
                foreach($fields as $field) {
                    if(strpos($field, '.') === false) {
                        $terms[] = [
                            "term" => [
                                $field . ".keyword" => [
                                    "value" => $value
                                ]
                            ]
                        ];
                    }
                }

                $termsNestedFields = [];

                foreach($nestedFields as $path => $nestedField) {
                    $termsNestedFields[$path] = array_map(function($field) use ($path, $value): array { 
                        return [
                            "term" => [
                                $path . "." . $field . ".keyword" => [
                                    "value" => $value
                                ]
                        ]];
                    }, $nestedField);
                }

                return [
                    "bool" => [
                        ($type === self::TYPE_NOT_EQUAL ? "must_not" : "should") => [
                            ...$terms,
                            ...$this->generateNestedFields($nestedFields, $termsNestedFields)
                        ]
                    ]
                ];
            case self::TYPE_CONTAINS:
            case self::TYPE_NOT_CONTAINS:
                $mathPhrase = [];

                foreach($fields as $field) {
                    if(strpos($field, '.') === false) {
                        $mathPhrase[] = [
                            "match_phrase_prefix" => [
                                $field => [
                                    "query" => $value
                                ]
                            ]
                        ];
                    }
                }

                $mathPhraseNested = [];

                foreach($nestedFields as $path => $nestedField) {
                    $mathPhraseNested[$path] = array_map(function($field) use ($path, $value): array { 
                        return [
                            "match_phrase_prefix" => [
                                $path . "." . $field => [
                                    "query" => $value
                                ]
                        ]];
                    }, $nestedField);
                }

                $query = [
                    "bool" => [
                        ($type === self::TYPE_NOT_CONTAINS ? "must_not" : "should") => [
                            ...$mathPhrase,
                            ...$this->generateNestedFields($nestedFields, $mathPhraseNested)
                        ]
                    ]
                ];

                return $query;
            case self::TYPE_EMPTY:
            case self::TYPE_NOT_EMPTY:
                $conditionMust = $type === self::TYPE_EMPTY ? "must_not" : "must";

                foreach($fields as $field) {
                    if(strpos($field, '.') === false) {
                        $terms[] = [
                            "bool" => [
                                 $conditionMust => [
                                    "exists" => [
                                        "field" => $field
                                    ]
                                ]
                            ]
                        ];
                    }
                }

                $termsNestedFields = [];

                foreach($nestedFields as $path => $nestedField) {
                    $termsNestedFields[$path] = array_map(function($field) use ($path, $conditionMust): array { 
                        return [
                            "bool" => [
                                 $conditionMust => [
                                    "exists" => [
                                        "field" => $path . "." . $field
                                    ]
                                ]
                            ]
                        ];
                    }, $nestedField);
                }

                return [
                    "bool" => [
                        ($type === self::TYPE_EMPTY ? "should" : "must") => [
                            ...$terms,
                            ...$this->generateNestedFields($nestedFields, $termsNestedFields)
                        ]
                    ]
                ];
            default:
                throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }
    }

    private function getNestedFields(array $fields): array
    {
        $nestedFields = [];

        foreach ($fields as $field) {
            if(strpos($field, '.') !== false) {
                $explodedFields = explode('.', $field) ?? [];
                $lastField = array_pop($explodedFields);

                $nestedFields[implode('.', $explodedFields)][] = $lastField;
            }
        }

        return $nestedFields;
    }

    private function generateNestedFields(array $nestedFields, array $query): array
    {
        $result = [];

        foreach(array_keys($nestedFields) as $path) {
            $explodedPath = explode('.', $path);

            $result[] = $this->generateNestedItems($explodedPath, $query);
        }

        return $result;
    }

    private function generateNestedItems(array $explodedPath, array $query, string $currentPath = ''): array
    {
        if(count($explodedPath) > 1) {
            foreach($explodedPath as $pathItem) {
                $nestedPath = !empty($currentPath) ? $currentPath . '.' . $pathItem : $pathItem;
    
                return [
                    "nested" => [
                        "path" =>  $nestedPath,
                        "query" => $this->generateNestedItems(
                            array_splice($explodedPath, 1, count($explodedPath)),
                            $query,
                            $nestedPath
                        ),
                    ]
                ];
            }
        }

        $nestedPath = !empty($currentPath) ? $currentPath . '.' . $explodedPath[0] : $explodedPath[0];

        return [
            "nested" => [
                "path" => $nestedPath,
                "query" => [
                    "bool" => [
                        "should" => $query[$nestedPath]
                    ]
                ]
            ]
        ];
    }
}
