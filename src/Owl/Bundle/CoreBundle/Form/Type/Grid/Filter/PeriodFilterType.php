<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Grid\Filter;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

final class PeriodFilterType extends AbstractType
{
    public function __construct(
        private AdminUserContextInterface $adminUserContext,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($case) => 'owl.grid.filter.period.type_' . $case->value, PeriodTypeEnum::cases()),
                    array_map(fn($case) => $case->value, PeriodTypeEnum::cases())
                ),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function ($event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data === null) {
                return;
            }

            if ($data['type'] === PeriodTypeEnum::TYPE_MONTH->value) {
                $this->createMonthFilter($form);
                $this->createYearFilter($form);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function ($event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data === null) {
                return;
            }

            if ($data['type'] === PeriodTypeEnum::TYPE_MONTH->value) {
                $this->createMonthFilter($form);
                $this->createYearFilter($form);
            }
        });
    }

    public function getBlockPrefix(): string
    {
        return 'owl_grid_filter_date';
    }

    private function createMonthFilter($form): void
    {
        $form->add('month', ChoiceType::class, [
            'choices' => $this->getLocalizedMonths($this->adminUserContext->getUser()->getLocaleCode()),
        ]);
    }

    private function createYearFilter($form): void
    {
        $form->add('year', ChoiceType::class, [
            'choices' => $this->getYearsRange(),
            'empty_data' => date('Y'),
        ]);
    }

    private function getLocalizedMonths(string $locale): array
    {
        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null,
            null,
            'LLLL' // pełna nazwa miesiąca zależna od lokalizacji
        );

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1);
            $monthName = $formatter->format($timestamp);
            // np. 'Styczeń' => 1
            $months[ucfirst($monthName)] = $i;
        }

        return $months;
    }

    private function getYearsRange(): array
    {
        $currentYear = (int) date('Y'); // bieżący rok
        $years = [];

        // Dodaj 5 lat wstecz i 1 rok do przodu
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $years[$i] = $i; // Wartość i etykieta to ten sam rok
        }

        return $years;
    }
}
