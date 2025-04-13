<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Owl\Bundle\AdminBundle\Form\Type\ContractorAutocompleteType;
use Owl\Component\Core\Model\Invoice\Buyer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InvoiceBuyerType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contractor', ContractorAutocompleteType::class, [
                'label' => 'owl.ui.contractor',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Buyer::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_buyer';
    }
}
