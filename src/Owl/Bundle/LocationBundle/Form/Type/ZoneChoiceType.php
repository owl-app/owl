<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Owl\Component\Location\Model\ZoneInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ZoneChoiceType extends AbstractType
{
    /**
     * @param RepositoryInterface<ZoneInterface> $zoneRepository
     */
    public function __construct(
        private readonly RepositoryInterface $zoneRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (): iterable {
                return $this->zoneRepository->findAll();
            },
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'label' => 'owl.form.address.zone',
            'placeholder' => 'owl.form.zone.select',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_zone_choice';
    }
}
