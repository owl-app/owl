<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type\Payment;

use Owl\Component\Invoice\Enum\InvoicePaymentMethodEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InvoicePaymentMethodChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => array_combine(
                    array_map(fn ($case) => 'owl.invoice.payment.methods.' . $case->value, InvoicePaymentMethodEnum::cases()),
                    array_map(fn ($case) => $case->value, InvoicePaymentMethodEnum::cases()),
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
        return 'owl_invoice_payment_method_choice';
    }
}
