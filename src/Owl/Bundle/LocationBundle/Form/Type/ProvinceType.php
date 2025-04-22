<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProvinceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'owl.form.province.name',
            ])
            ->add('abbreviation', TextType::class, [
                'label' => 'owl.form.province.abbreviation',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_province';
    }
}
