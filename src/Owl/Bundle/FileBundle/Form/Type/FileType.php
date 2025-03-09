<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class FileType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'owl.form.common.name',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'owl.form.common.description',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_file';
    }
}
