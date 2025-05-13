<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\ContractorBundle\Form\Type\ContractorType as BaseContractorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class ContractorType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'owl.form.admin.currency',
                'required' => false,
                'multiple' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return BaseContractorType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_contractor';
    }
}
