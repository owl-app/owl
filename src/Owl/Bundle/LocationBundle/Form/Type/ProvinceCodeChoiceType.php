<?php

declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Form\Type;

use Owl\Component\Location\Model\ProvinceInterface;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class ProvinceCodeChoiceType extends AbstractType
{
    /** @param RepositoryInterface<ProvinceInterface> $provinceRepository */
    public function __construct(private RepositoryInterface $provinceRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->provinceRepository, 'code')));
    }

    public function getParent(): string
    {
        return ProvinceChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_province_code_choice';
    }
}
