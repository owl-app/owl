<?php

declare(strict_types=1);

namespace Owl\Component\Core\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class DateFilter implements FilterInterface
{
    public const NAME = 'date';

    public const DEFAULT_INCLUSIVE_FROM = true;

    public const DEFAULT_INCLUSIVE_TO = false;

    /**
     * @param array<string, mixed> $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options): void
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        $field = (string) $this->getOption($options, 'field', $name);

        $from = $data['from'] ?? null;
        if ($from) {
            $inclusive = (bool) $this->getOption($options, 'inclusive_from', self::DEFAULT_INCLUSIVE_FROM);
            if (true === $inclusive) {
                $dataSource->restrict($expressionBuilder->greaterThanOrEqual($field, $from));
            } else {
                $dataSource->restrict($expressionBuilder->greaterThan($field, $from));
            }
        }

        $to = $data['to'] ?? null;
        if ($to) {
            $inclusive = (bool) $this->getOption($options, 'inclusive_to', self::DEFAULT_INCLUSIVE_TO);
            if (true === $inclusive) {
                $dataSource->restrict($expressionBuilder->lessThanOrEqual($field, $to));
            } else {
                $dataSource->restrict($expressionBuilder->lessThan($field, $to));
            }
        }
    }

    /**
     * @param array<string, mixed> $options
     * @param mixed $default
     *
     * @return mixed
     */
    private function getOption(array $options, string $name, $default)
    {
        return $options[$name] ?? $default;
    }
}
