<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'owl_admin_company',
    route: 'owl_admin_entity_autocomplete',
)]
final class CompanyAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $companyClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->companyClass,
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
        return 'owl_admin_company_autocomplete';
    }
}
