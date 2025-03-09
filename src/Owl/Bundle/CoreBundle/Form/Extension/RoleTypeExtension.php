<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Form\Extension;

use Owl\Bundle\CoreBundle\Form\Type\Rbac\RoleSettingType;
use Owl\Bundle\RbacBundle\Form\Type\RoleType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RoleTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $data = $builder->getData();

        $builder
            ->add('setting', RoleSettingType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'owl.ui.canonical_name',
                'disabled' => $data->getId() !== null ? true : false,
            ])
        ;
    }

    /**
     * @return RoleType::class
     */
    public function getExtendedType(): string
    {
        return RoleType::class;
    }

    /**
     * @return list{RoleType::class}
     */
    public static function getExtendedTypes(): iterable
    {
        return [RoleType::class];
    }
}
