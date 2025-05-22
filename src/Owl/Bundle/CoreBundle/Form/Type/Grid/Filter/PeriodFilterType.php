<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Grid\Filter;

use Owl\Bundle\CoreBundle\Form\Type\Date\MonthType;
use Owl\Bundle\CoreBundle\Form\Type\Date\QuarterType;
use Owl\Bundle\CoreBundle\Form\Type\Date\YearType;
use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PeriodFilterType extends AbstractType
{
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
            $this->createFields($event);
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function ($event) {
            $this->createFields($event);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'owl_grid_filter_date';
    }

    private function createFields(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null) {
            return;
        }

        if ($data['type'] === PeriodTypeEnum::TYPE_MONTH->value) {
            $form->add('month', MonthType::class);
        }

        if ($data['type'] === PeriodTypeEnum::TYPE_QUARTER->value) {
            $form->add('quarter', QuarterType::class);
        }

        if ($data['type'] !== PeriodTypeEnum::TYPE_ALL->value) {
            $form->add('year', YearType::class);
        }
    }
}
