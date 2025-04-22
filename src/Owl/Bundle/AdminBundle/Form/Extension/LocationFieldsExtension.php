<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Extension;

use Owl\Bundle\LocationBundle\Form\Type\ProvinceCodeChoiceType;
use Owl\Component\Location\Repository\CountryRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

final class LocationFieldsExtension extends AbstractTypeExtension
{
    public function __construct(private CountryRepositoryInterface $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->addDependent('provinceCode', 'countryCode', function (DependentField $field, ?string $countryCode = null) {
                if (null === $countryCode) {
                    return;
                }

                $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

                if ($country->hasProvinces()) {
                    $field->add(ProvinceCodeChoiceType::class, [
                        'country' => $country,
                        'placeholder' => 'owl.form.province.select',
                        'label' => 'owl.form.address.province',
                        'auto_initialize' => false,
                        'required' => false,
                    ]);
                }
            })
        ;

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $formEvent) {
                /** @var TaxRateInterface $data */
                $data = $formEvent->getData();
                $form = $formEvent->getForm();

                $form->has('provinceCode') ?: $data->setProvinceCode(null);
            },
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [];
    }
}
