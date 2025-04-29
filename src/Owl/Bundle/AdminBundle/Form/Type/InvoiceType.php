<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceSerieHiddenType;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceBuyerType;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\LineItemType;
use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType as BaseInvoiceType;
use Owl\Component\Core\Model\Invoice\BuyerInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                'entry_options' => [
                    'calculate_values_from' => $options['calculate_values_from'],
                ],
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
            ->add('calculateValuesFrom', ChoiceType::class, [
                'label' => 'owl.ui.method_of_converting_amounts',
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => false,
                'data' => 'net',
                'attr' => [
                    'class' => 'd-flex justify-content-center gap-5',
                ],
                'label_attr' => [
                    'class' => 'p-0',
                ],
                'choice_attr' => function () {
                    return ['class' => 'mb-0', 'data-action' => 'change->invoice-form#calculateValuesFromChanged'];
                },
                'choices' => array_combine(
                    array_map(fn($case) => 'owl.invoice.calculate_values_from.' . $case->value, CalculateValuesFromEnum::cases()),
                    array_map(fn($case) => $case->value, CalculateValuesFromEnum::cases())
                ),
            ]);
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

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $oldLineItems = $form->get('lineItems')->getConfig();

            $form->add(
                $oldLineItems->getName(),
                $oldLineItems->getType()->getInnerType()::class,
                array_replace(
                    $oldLineItems->getOptions(), 
                    [
                        'entry_options' => [
                            'calculate_values_from' => $data['calculateValuesFrom'],
                        ]
                    ]
                )
            );
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'calculate_values_from' => 'net',
        ]);
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
