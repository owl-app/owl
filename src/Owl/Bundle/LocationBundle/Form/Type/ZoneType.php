<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Owl\Bundle\LocationBundle\Form\EventListener\BuildZoneFormSubscriber;
use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ZoneType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'owl.form.zone.name',
            ])
            ->add('type', ZoneTypeChoiceType::class, [
                'disabled' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var ZoneInterface $zone */
            $zone = $event->getData();

            $entryOptions = [
                'entry_type' => $this->getZoneMemberEntryType($zone->getType()),
                'entry_options' => $this->getZoneMemberEntryOptions($zone->getType()),
            ];

            if ($zone->getType() === ZoneInterface::TYPE_ZONE) {
                $entryOptions['entry_options']['choice_filter'] = static fn (?ZoneInterface $subZone): bool => $subZone !== null && $zone->getId() !== $subZone->getId();
            }

            $event->getForm()->add('members', CollectionType::class, [
                'entry_type' => ZoneMemberType::class,
                'entry_options' => $entryOptions,
                'button_add_label' => 'owl.form.zone.add_member',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
            ]);
        });

        if ($options['add_build_zone_form_subscriber']) {
            $builder->addEventSubscriber(new BuildZoneFormSubscriber());
        }
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_zone';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'add_build_zone_form_subscriber' => true,
            ])
        ;
    }

    private function getZoneMemberEntryType(string $zoneMemberType): string
    {
        $zoneMemberEntryTypes = [
            ZoneInterface::TYPE_COUNTRY => CountryCodeChoiceType::class,
            ZoneInterface::TYPE_PROVINCE => ProvinceCodeChoiceType::class,
            ZoneInterface::TYPE_ZONE => ZoneCodeChoiceType::class,
        ];

        return $zoneMemberEntryTypes[$zoneMemberType];
    }

    private function getZoneMemberEntryOptions(string $zoneMemberType): array
    {
        $zoneMemberEntryOptions = [
            ZoneInterface::TYPE_COUNTRY => [
                'label' => 'owl.form.zone.types.country',
                'enabled' => false,
                'attr' => ['class' => 'country_search_dropdown ui fluid search selection dropdown'],
            ],
            ZoneInterface::TYPE_PROVINCE => ['label' => 'owl.form.zone.types.province'],
            ZoneInterface::TYPE_ZONE => ['label' => 'owl.form.zone.types.zone'],
        ];

        return $zoneMemberEntryOptions[$zoneMemberType];
    }
}
