<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusGrid\Sorting;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Validation\FieldValidator;
use Sylius\Component\Grid\Validation\FieldValidatorInterface;
use Sylius\Component\Grid\Validation\SortingParametersValidator;
use Sylius\Component\Grid\Validation\SortingParametersValidatorInterface;

final class Sorter implements SorterInterface
{
    private SortingParametersValidatorInterface $sortingValidator;

    private FieldValidatorInterface $fieldValidator;

    public function __construct(?SortingParametersValidatorInterface $sortingValidator = null, ?FieldValidatorInterface $fieldValidator = null)
    {
        $this->sortingValidator = $sortingValidator ?? new SortingParametersValidator();
        $this->fieldValidator = $fieldValidator ?? new FieldValidator();
    }

    public function sort(Grid $grid, Parameters $parameters): array
    {
        $dataSort = [];
        $enabledFields = $grid->getFields();

        $sorting = $parameters->get('sorting', $grid->getSorting());
        $this->sortingValidator->validateSortingParameters($sorting, $enabledFields);

        foreach ($sorting as $field => $order) {
            $this->fieldValidator->validateFieldName($field, $enabledFields);
            $gridField = $grid->getField($field);
            $property = $gridField->getSortable();

            if (null !== $property) {
                $sortProperty = "$property.keyword:$order";

                if (strpos($property, '.') !== false) {
                    $explodedProperty = explode('.', $property);
                    $sortProperty = $sortProperty . ":nested:" . array_shift($explodedProperty);
                }

                $dataSort[] = $sortProperty;
            }
        }

        return $dataSort;
    }
}
