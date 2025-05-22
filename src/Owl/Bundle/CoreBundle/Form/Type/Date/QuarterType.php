<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Date;

use DateTime;
use Owl\Component\Core\Enum\Grid\Filter\PeriodQuarterEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QuarterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_combine(
                array_map(fn($case) => 'owl.grid.filter.period.quarter_type_q' . $case->value, PeriodQuarterEnum::cases()),
                array_map(fn($case) => $case->value, PeriodQuarterEnum::cases())
            ),
            'empty_data' => PeriodQuarterEnum::fromDate(new DateTime())->value,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_month_choice';
    }
}
