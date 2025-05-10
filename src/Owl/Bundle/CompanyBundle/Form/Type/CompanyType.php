<?php

declare(strict_types=1);

namespace Owl\Bundle\CompanyBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CompanyType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'owl.company.name',
                'required' => true
            ])
            ->add('taxNumber', TextType::class, [
                'label' => 'owl.company.tax_number',
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'owl.company.city',
                'required' => true
            ])
            ->add('street', TextType::class, [
                'label' => 'owl.company.street',
                'required' => true
            ])
            ->add('buildingNumber', TextType::class, [
                'label' => 'owl.company.building_number',
            ])
            ->add('flatNumber', TextType::class, [
                'required' => false,
                'label' => 'owl.company.flat_number',
            ])
            ->add('postCode', TextType::class, [
                'label' => 'owl.company.post_code',
                'required' => true
            ])
            ->add('phone', TextType::class, [
                'label' => 'owl.company.phone',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'owl.company.email',
                'required' => true
            ])
            ->add('contactPerson', TextType::class, [
                'label' => 'owl.company.contact_person',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'owl.ui.description',
                'attr' => [
                    'style' => 'min-height: 6rem;',
                ],
                'required' => false
            ])
            ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_company';
    }
}
