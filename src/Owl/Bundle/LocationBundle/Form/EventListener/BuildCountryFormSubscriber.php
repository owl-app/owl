<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\EventListener;

use Doctrine\Persistence\ObjectRepository;
use Owl\Bundle\LocationBundle\Form\Type\ProvinceCodeChoiceType;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @internal
 */
final class BuildCountryFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private ObjectRepository $countryRepository, private FormFactoryInterface $formFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        /** @var CountryCodeAwareInterface&ProvinceCodeAwareInterface $resource */
        $resource = $event->getData();

        if (null === $resource) {
            return;
        }

        $countryCode = $resource->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        /** @var CountryInterface|null $country */
        $this->addFieldProvinceCode($event, $countryCode);
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        $this->addFieldProvinceCode($event, $data['countryCode']);
    }

    private function addFieldProvinceCode(FormEvent $event, string $countryCode): void
    {
        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        if (null === $country) {
            return;
        }

        $form = $event->getForm();

        if ($country->hasProvinces()) {
            $form->add($this->createProvinceCodeChoiceForm($country));
        }
    }

    private function createProvinceCodeChoiceForm(CountryInterface $country, ?string $provinceCode = null): FormInterface
    {
        return $this->formFactory->createNamed('provinceCode', ProvinceCodeChoiceType::class, $provinceCode, [
            'country' => $country,
            'auto_initialize' => false,
            'label' => 'owl.form.address.province',
            'placeholder' => 'owl.form.province.select',
        ]);
    }
}
