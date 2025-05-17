<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExchangeRateSnapshot extends AbstractResourceType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ratio', NumberType::class, [
                'label' => 'sylius.form.exchange_rate.ratio',
                'required' => true,
                'invalid_message' => 'sylius.exchange_rate.ratio.invalid',
                'scale' => 10,
                'rounding_mode' => $options['rounding_mode'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('rounding_mode', \NumberFormatter::ROUND_HALFEVEN);
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice_exchange_rate_snapshot';
    }
}
