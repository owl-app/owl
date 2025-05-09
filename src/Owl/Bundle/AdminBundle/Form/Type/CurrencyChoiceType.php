<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyChoiceType extends AbstractType
{
    /** @param RepositoryInterface<CurrencyInterface> $currencyRepository */
    public function __construct(private RepositoryInterface $currencyRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $currencies = $this->currencyRepository->findAll();
        $firstCurrency = $currencies[0] ?? null;

        $resolver->setDefaults([
            'choices' => $currencies,
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'data' => $firstCurrency,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_currency_choice';
    }
}
