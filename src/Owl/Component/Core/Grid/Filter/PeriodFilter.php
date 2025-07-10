<?php

declare(strict_types=1);

namespace Owl\Component\Core\Grid\Filter;

use Owl\Component\Core\Enum\Grid\Filter\PeriodQuarterEnum;
use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/** @experimental */
final class PeriodFilter implements FilterInterface
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options = []): void
    {
        if (empty($data)) {
            return;
        }

        $expressionBuilder = $dataSource->getExpressionBuilder();
        $field = $options['field'] ?? $name;
        $type = $data['type'] ?? ($options['type'] ?? '');

        if ($type == PeriodTypeEnum::TYPE_ALL->value) {
            return;
        }

        /** @var array<string, mixed> $data */
        $expressions = $this->getExpression($expressionBuilder, (string) $type, (string) $field, $data);

        $dataSource->restrict($expressionBuilder->andX(...$expressions));
    }

    /**
     * @param array<string, mixed> $value
     *
     * @return array<int, mixed>
     *
     * @throws \InvalidArgumentException
     */
    private function getExpression(
        ExpressionBuilderInterface $expressionBuilder,
        string $type,
        string $field,
        array $value,
    ): array {
        $start = null;
        $end = null;

        switch ($type) {
            case PeriodTypeEnum::TYPE_MONTH->value:
                if (!isset($value['year']) || !isset($value['month'])) {
                    throw new \InvalidArgumentException('Missing "year" or "month" in value for TYPE_MONTH.');
                }
                $startDate = new \DateTime($value['year'] . '-' . $value['month'] . '-01');
                $start = $startDate->format('Y-m-d');
                $end = $startDate->modify('last day of this month')->format('Y-m-d');

                break;
            case PeriodTypeEnum::TYPE_QUARTER->value:
                if (!isset($value['year']) || !isset($value['quarter'])) {
                    throw new \InvalidArgumentException('Missing "year" or "quarter" in value for TYPE_QUARTER.');
                }
                $range = PeriodQuarterEnum::getPeriodRange((string) $value['quarter']);
                $start = $value['year'] . '-' . $range['start'];
                $end = $value['year'] . '-' . $range['end'];

                break;
            case PeriodTypeEnum::TYPE_YEAR->value:
                if (!isset($value['year'])) {
                    throw new \InvalidArgumentException('Missing "year" in value for TYPE_YEAR.');
                }
                $start = $value['year'] . '-01-01';
                $end = $value['year'] . '-12-31';

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }

        return [
            $expressionBuilder->greaterThanOrEqual(
                $field,
                $start,
            ),
            $expressionBuilder->lessThanOrEqual(
                $field,
                $end,
            ),
        ];
    }
}
