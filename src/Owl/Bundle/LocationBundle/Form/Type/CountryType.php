<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class CountryType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber(\Symfony\Component\Form\Extension\Core\Type\CountryType::class))
            ->add('provinces', CollectionType::class, [
                'entry_type' => ProvinceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_label' => 'owl.form.country.add_province',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_country';
    }
}
