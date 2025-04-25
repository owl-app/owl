<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type\Taxation;

use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxRateChoiceType extends AbstractType
{
    /** @param RepositoryInterface<TaxRateInterface> $taxRateRepository */
    public function __construct(private RepositoryInterface $taxRateRepository)
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
        $resolver
            ->setDefaults([
                'choice_filter' => null,
                'choices' => function (Options $options): iterable {
                    return $this->taxRateRepository->findAll();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'enabled' => true,
                'label' => 'owl.invoice.line_item.tax_rate.label',
                'placeholder' => 'owl.invoice.line_item.tax_rate.select',
            ])
            ->setAllowedTypes('choice_filter', ['null', 'callable'])
            ->setNormalizer('choices', static function (Options $options, array $countries): array {
                if ($options['choice_filter']) {
                    $countries = array_filter($countries, $options['choice_filter']);
                }

                usort($countries, static fn (TaxRateInterface $firstTaxRate, TaxRateInterface $secondTaxRate): int => $firstTaxRate->getName() <=> $secondTaxRate->getName());

                return $countries;
            })
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_tax_rate_choice';
    }
}
