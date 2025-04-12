<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceSerieType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('format', TextType::class, [
                'label' => 'owl.invoice.format_number',
            ])
            ->add('invoiceType', InvoiceTypeChoiceType::class, [
                'label' => 'owl.invoice.type',
            ])
            ->add('sequenceIncrement', InvoiceSequenceIncrementStrategyChoiceType::class, [
                'label' => 'owl.invoice.sequence_increment',
            ])
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
                'label' => 'owl.invoice.is_default_serie',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_serie';
    }
}
