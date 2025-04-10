<?php

declare(strict_types=1);

namespace Owl\Bundle\ContractorBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ContractorType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'owl.contractor.company_name',
            ])
            ->add('taxNumber', TextType::class, [
                'label' => 'owl.contractor.tax_number',
            ])
            ->add('street', TextType::class, [
                'label' => 'owl.contractor.street',
            ])
            ->add('buildingNumber', TextType::class, [
                'label' => 'owl.contractor.building_number',
            ])
            ->add('flatNumber', TextType::class, [
                'required' => false,
                'label' => 'owl.contractor.flat_number',
            ])
            ->add('city', TextType::class, [
                'label' => 'owl.contractor.city',
            ])
            ->add('postCode', TextType::class, [
                'label' => 'owl.contractor.post_code',
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'label' => 'owl.contractor.email',
            ])
        ;
    }

    /**
     * @return 'owl_contractor'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_contractor';
    }
}
