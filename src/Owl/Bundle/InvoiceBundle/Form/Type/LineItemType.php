<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Owl\Bundle\InvoiceBundle\Form\Type\Taxation\TaxRateChoiceType;
use Owl\Component\Invoice\Model\LineItem;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class LineItemType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'owl.invoice.line_item.name',
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'owl.invoice.line_item.quantity',
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => array_flip(LineItem::getUnitLabels()),
                'label' => 'owl.invoice.line_item.unit',
            ])
            ->add('unitPrice', MoneyType::class, [
                'required' => false,
                'label' => 'owl.invoice.line_item.unit_price',
            ])
            ->add('taxRate', TaxRateChoiceType::class, [
                'label' => 'owl.invoice.line_item.tax_rate.label',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_line_item';
    }
}
