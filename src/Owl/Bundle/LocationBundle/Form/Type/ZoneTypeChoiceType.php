<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Owl\Component\Location\Model\ZoneInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ZoneTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => [
                    'owl.form.zone.types.country' => ZoneInterface::TYPE_COUNTRY,
                    'owl.form.zone.types.province' => ZoneInterface::TYPE_PROVINCE,
                    'owl.form.zone.types.zone' => ZoneInterface::TYPE_ZONE,
                ],
                'label' => 'owl.form.zone.type',
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_zone_type_choice';
    }
}
