<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\CompanyBundle\Form\Type\CompanyType as BaseCompanyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class CompanyType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'owl.form.invoice.currency',
                'required' => false,
                'multiple' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return BaseCompanyType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_company';
    }
}
