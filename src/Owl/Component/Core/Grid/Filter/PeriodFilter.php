<?php

declare(strict_types=1);

namespace Owl\Component\Core\Grid\Filter;

use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

/** @experimental */
final class PeriodFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if (empty($data)) {
            return;
        }

        $expressionBuilder = $dataSource->getExpressionBuilder();
        $field = $options['field'] ?? $name;
        $type = $data['type'] ?? ($options['type']);

        // $expressions = [];

        // $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    private function getExpression(
        ExpressionBuilderInterface $expressionBuilder,
        string $type,
        string $field,
        $value,
    ) {
        switch ($type) {
            case PeriodTypeEnum::TYPE_ALL->value:
                
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }
    }
}
