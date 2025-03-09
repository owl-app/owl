<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Type\Rbac;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RoleSettingType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayName', TextType::class, [
                'label' => 'owl.form.role.name',
                'empty_data' => null,
            ])
            ->add('theme', ThemeNameChoiceType::class, [
                'label' => 'owl.form.role.choice_theme',
                'empty_data' => null,
                'placeholder' => 'owl.ui.no_theme',
            ])
        ;
    }

    /**
     * @return 'owl_rbac_role_setting'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_rbac_role_setting';
    }
}
