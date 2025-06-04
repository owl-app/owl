<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Extension;

use Owl\Bundle\CompanyBundle\Form\Type\CompanyType;
use Owl\Bundle\LocationBundle\Form\EventListener\BuildCountryFormSubscriber;
use Owl\Bundle\LocationBundle\Form\Type\CountryCodeChoiceType;
use Owl\Bundle\LocationBundle\Form\Type\ZoneChoiceType;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Repository\ZoneRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class LocationFieldsExtension extends AbstractTypeExtension
{
    public function __construct(
        private BuildCountryFormSubscriber $buildCountryFormSubscriber,
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'owl.form.address.country',
                'enabled' => true,
            ])
        ;

        $builder->addEventSubscriber($this->buildCountryFormSubscriber);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var (ProvinceCodeAwareInterface&CountryCodeAwareInterface)|null $data */
                $data = $event->getData();

                if (null === $data) {
                    return;
                }

                $countryCode = $data->getCountryCode();
                $provinceCode = $data->getProvinceCode();

                if (null === $countryCode) {
                    return;
                }

                $this->createZoneChoiceForm($countryCode, $provinceCode, $event->getForm());
            },
            -100,
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                if (!is_array($data) || !array_key_exists('countryCode', $data) || empty($data['countryCode'])) {
                    return;
                }

                $this->createZoneChoiceForm($data['countryCode'], $data['provinceCode'] ?? null, $event->getForm());
            },
            -100,
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $formEvent) {
                /** @var (ProvinceCodeAwareInterface&CountryCodeAwareInterface)|null $data */
                $data = $formEvent->getData();
                $form = $formEvent->getForm();

                $form->has('provinceCode') ?: $data->setProvinceCode(null);
            },
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            CompanyType::class,
        ];
    }

    private function createZoneChoiceForm(string $countryCode, ?string $provinceCode, FormInterface $form): void
    {
        if ($form->has('provinceCode') && empty($provinceCode)) {
            $form->remove('zone');

            return;
        }

        $zones = $this->zoneRepository->findAllByCountryAndProvince($countryCode, $provinceCode);

        $form->add('zone', ZoneChoiceType::class, [
            'choices' => $zones,
            'label' => 'owl.form.address.zone',
            'auto_initialize' => false,
            'required' => true,
        ]);
    }
}
