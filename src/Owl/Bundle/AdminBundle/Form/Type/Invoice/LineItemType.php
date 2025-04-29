<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Owl\Bundle\InvoiceBundle\Form\Type\LineItemType as BaseLineItemType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LineItemType extends AbstractType
{
    public function __construct(
        private EventSubscriberInterface $taxRateSnapshotEventSubscriber,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('net' === $options['calculate_values_from']) {
            $builder
                ->add('unitPrice', MoneyType::class, [
                    'required' => false,
                    'label' => 'owl.invoice.line_item.unit_price',
                ])
                ->add('subtotal', MoneyType::class, [
                    'required' => false,
                    'label' => 'owl.invoice.line_item.subtotal',
                    'mapped' => false,
                ]);
        }

        if ('gross' === $options['calculate_values_from']) {
            $builder
                ->add('unitPriceGross', MoneyType::class, [
                    'required' => false,
                    'label' => 'owl.invoice.line_item.unit_price_gross',
                ])
                ->add('total', MoneyType::class, [
                    'required' => false,
                    'label' => 'owl.invoice.line_item.total',
                    'mapped' => false,
                ]);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            if ($event->getForm()->has('subtotal')) {
                $event->getForm()->get('subtotal')->setData($event->getData()?->getSubtotal());
            }
        });

        $builder->addEventSubscriber($this->taxRateSnapshotEventSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'calculate_values_from' => 'net',
        ]);
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
