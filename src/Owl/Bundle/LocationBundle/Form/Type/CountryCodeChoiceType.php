<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Owl\Component\Location\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class CountryCodeChoiceType extends AbstractType
{
    /** @param RepositoryInterface<CountryInterface> $countryRepository */
    public function __construct(private RepositoryInterface $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->countryRepository, 'code')));
    }

    public function getParent(): string
    {
        return CountryChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_country_code_choice';
    }
}
