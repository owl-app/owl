<?php

declare(strict_types=1);

namespace Owl\Component\Core\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class PeriodFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $expressionBuilder = $dataSource->getExpressionBuilder();
        $fields = $options['fields'] ?? [$name];

        Assert::string($data);
        $values = explode(',', $data);

        $expressions = [];
        foreach ($fields as $field) {
            foreach ($values as $value) {
                $expressions[] = $expressionBuilder->equals($field, $value);
            }
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }
}
