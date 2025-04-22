<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ZoneMemberType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', $options['entry_type'], array_merge($options['entry_options'], ['required' => true]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('entry_type')
            ->setDefaults([
                'entry_options' => [],
                'placeholder' => 'owl.form.zone_member.select',
                'data_class' => $this->dataClass,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_zone_member';
    }
}
