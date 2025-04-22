<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Owl\Component\Location\Model\CountryInterface;
use Owl\Component\Location\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProvinceChoiceType extends AbstractType
{
    /** @param RepositoryInterface<ProvinceInterface> $provinceRepository */
    public function __construct(private RepositoryInterface $provinceRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options): iterable {
                if (null === $options['country']) {
                    return $this->provinceRepository->findAll();
                }

                return $options['country']->getProvinces();
            },
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'country' => null,
            'label' => 'owl.form.address.province',
            'placeholder' => 'owl.form.province.select',
        ]);
        $resolver->addAllowedTypes('country', ['null', CountryInterface::class]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_province_choice';
    }
}
