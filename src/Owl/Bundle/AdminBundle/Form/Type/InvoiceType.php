<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceSerieHiddenType;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\LineItemType;
use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType as BaseInvoiceType;
use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class InvoiceType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $builder->getData();

        $builder
            ->add('company', CompanyAutocompleteType::class, [
                'label' => 'owl.ui.company',
                'required' => true,
                'choice_label' => function (CompanyInterface $choice, string $key, string $value) use ($invoice): string {
                    // if ($invoice->getId() !== null && $invoice->getBuyer()?->getCompany() !== $choice->getCompanyName()) {
                    //     return $invoice->getBuyer()->getCompany() .' -> ' . $choice->getCompanyName();
                    // }

                    return $choice->getName();
                },
                'attr' => [
                    'data-controller' => 'invoice-addable-autocomplete',
                    'data-action' => 'form:company:created@window->invoice-addable-autocomplete#addOption',
                    'data-invoice-addable-autocomplete-text-by-value' => 'name',
                    'data-invoice-addable-autocomplete-action-after-change-value' => 'companyChanged',
                    'class' => 'addable-autocomplete'
                ],
            ])
            ->add('contractor', ContractorAutocompleteType::class, [
                'label' => 'owl.ui.contractor',
                'required' => true,
                'choice_label' => function (ContractorInterface $choice, string $key, string $value) use ($invoice): string {
                    if ($invoice->getId() !== null && $invoice->getBuyer()?->getCompany() !== $choice->getCompanyName()) {
                        return $invoice->getBuyer()->getCompany() .' -> ' . $choice->getCompanyName();
                    }

                    return $choice->getCompanyName();
                },
                'attr' => [
                    'data-controller' => 'invoice-addable-autocomplete',
                    'data-action' => 'form:contractor:created@window->invoice-addable-autocomplete#addOption',
                    'data-invoice-addable-autocomplete-text-by-value' => 'companyName',
                    'data-invoice-addable-autocomplete-action-after-change-value' => 'contractorChanged',
                    'class' => 'addable-autocomplete'
                ],
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
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'owl.form.invoice.currency',
                'required' => false,
                'multiple' => false,
            ])
            ->add('calculateValuesFrom', ChoiceType::class, [
                'label' => 'owl.ui.method_of_converting_amounts',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => false,
                'attr' => [
                    'class' => 'd-flex justify-content-center gap-5',
                ],
                'label_attr' => [
                    'class' => 'p-0',
                ],
                'choice_attr' => function () {
                    return ['class' => 'mb-0'];
                },
                'choices' => array_combine(
                    array_map(fn($case) => 'owl.invoice.calculate_values_from.' . $case->value, CalculateValuesFromEnum::cases()),
                    array_map(fn($case) => $case->value, CalculateValuesFromEnum::cases())
                ),
            ]);
        ;
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
