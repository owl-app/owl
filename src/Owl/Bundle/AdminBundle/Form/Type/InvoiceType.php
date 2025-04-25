<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceSerieHiddenType;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceBuyerType;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\LineItemType;
use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType as BaseInvoiceType;
use Owl\Component\Core\Model\Invoice\BuyerInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class InvoiceType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('buyer', InvoiceBuyerType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('fullNumber', HiddenType::class, [
                'required' => true,
            ])
            ->add('serie', InvoiceSerieHiddenType::class)
            ->add('lineItems', LiveCollectionType::class, [
                'entry_type' => LineItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'owl.ui.invoice.add_line_item',
                    'attr' => [
                        'class' => 'btn btn-secondary w-auto ps-5 pe-5 mt-3',
                    ],
                ],
                'button_delete_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'mb-0',
                    ]
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var InvoiceInterface $invoice */
            $invoice = $event->getData();
            /** @var BuyerInterface $buyer */
            $buyer = $invoice->getBuyer();

            if ($buyer && $buyer->getContractor()) {
                $buyer->importContractorData($buyer->getContractor());
            }
        });
    }

    public function getParent(): string
    {
        return BaseInvoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice';
    }
}
