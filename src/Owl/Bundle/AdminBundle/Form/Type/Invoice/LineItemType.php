<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Owl\Bundle\InvoiceBundle\Form\Type\LineItemType as BaseLineItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class LineItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subtotal', MoneyType::class, [
                'required' => false,
                'label' => 'owl.invoice.line_item.subtotal',
                'mapped' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $event->getForm()->get('subtotal')->setData($event->getData()?->getSubtotal());
        });
    }

    public function getParent(): string
    {
        return BaseLineItemType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice_line_item';
    }
}
