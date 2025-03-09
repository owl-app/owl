<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class UploadFileType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'sylius.form.image.type',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'sylius.form.image.file',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_file';
    }
}
