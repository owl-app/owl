<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Date;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class YearType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getYearsRange(),
            'empty_data' => date('Y'),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_year_choice';
    }

    /**
     * @return array<int, int>
     */
    private function getYearsRange(): array
    {
        $currentYear = (int) date('Y');
        $years = [];

        for ($i = $currentYear - 5; $i <= $currentYear + 1; ++$i) {
            $years[$i] = $i;
        }

        return $years;
    }
}