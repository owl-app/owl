<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\LocationBundle\Form\Type\CountryType as BaseCountryType;
use Owl\Bundle\LocationBundle\Form\Type\ProvinceType;
use Owl\Component\Location\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType as SymfonyCountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Countries;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class CountryType extends AbstractType
{
    /** @param RepositoryInterface<CountryInterface> $countryRepository */
    public function __construct(private readonly RepositoryInterface $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $options = [
                'label' => 'owl.form.country.name',
                'choice_loader' => null,
            ];

            $country = $event->getData();
            if ($country instanceof CountryInterface && null !== $country->getCode()) {
                $options['disabled'] = true;
                $options['choices'] = [$this->getCountryName($country->getCode()) => $country->getCode()];
            } else {
                $options['choices'] = array_flip($this->getAvailableCountries());
            }

            $form = $event->getForm();
            $form->add('code', SymfonyCountryType::class, $options);
        });

        $builder
            ->add('provinces', LiveCollectionType::class, [
                'entry_type' => ProvinceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'owl.form.country.add_province',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'owl.form.country.enabled',
            ])
        ;
    }

    public function getParent(): string
    {
        return BaseCountryType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_country';
    }

    private function getCountryName(string $code): string
    {
        return Countries::getName($code);
    }

    /** @return string[] */
    private function getAvailableCountries(): array
    {
        $availableCountries = Countries::getNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();

        foreach ($definedCountries as $country) {
            unset($availableCountries[$country->getCode()]);
        }

        return $availableCountries;
    }
}
