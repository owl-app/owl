<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'owl_admin_contrator',
    route: 'owl_admin_entity_autocomplete',
)]
final class ContractorAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $contractorClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->contractorClass,
            'choice_label' => 'companyName',
            'choice_value' => 'id',
            'attr' => [
                'data-controller' => 'contractor-autocomplete',
            ],
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_contractor_autocomplete';
    }
}
