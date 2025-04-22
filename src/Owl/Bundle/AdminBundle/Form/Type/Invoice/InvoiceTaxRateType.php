<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Owl\Bundle\AdminBundle\Form\Type\ZoneAutocompleteType;
use Owl\Bundle\InvoiceBundle\Form\Type\Taxation\TaxRateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceTaxRateType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('zones', ZoneAutocompleteType::class, [
                'label' => 'owl.ui.zones',
                'required' => true,
                'multiple' => true,
            ])
        ;
    }

    public function getParent(): string
    {
        return TaxRateType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice_tax_rate';
    }
}
