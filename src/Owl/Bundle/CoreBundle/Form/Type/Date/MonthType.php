<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Date;

use Owl\Component\Core\Translation\MonthTranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MonthType extends AbstractType
{
    public function __construct(
        private MonthTranslatorInterface $monthTranslator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getLocalizedMonths(),
            'empty_data' => date('n'),
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

    private function getLocalizedMonths(): array
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = $this->monthTranslator->translate($i);
            $months[ucfirst($monthName)] = $i;
        }

        return $months;
    }
}
