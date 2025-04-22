<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'owl_admin_zone',
    route: 'owl_admin_entity_autocomplete',
)]
final class ZoneAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $zoneClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->zoneClass,
            'choice_label' => 'name',
            'choice_value' => 'id',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_zone_autocomplete';
    }
}
