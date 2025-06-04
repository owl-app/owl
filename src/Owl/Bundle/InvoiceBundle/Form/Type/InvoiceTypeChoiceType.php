<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Owl\Component\Invoice\Enum\InvoiceTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InvoiceTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => array_combine(
                    array_map(fn ($case) => 'owl.invoice.types.' . $case->value, InvoiceTypeEnum::cases()),
                    array_map(fn ($case) => $case->value, InvoiceTypeEnum::cases()),
                ),
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_type_choice';
    }
}
