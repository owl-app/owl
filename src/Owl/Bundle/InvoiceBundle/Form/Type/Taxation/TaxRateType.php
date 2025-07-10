<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type\Taxation;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxRateType extends AbstractResourceType
{
    /**
     * @param string[] $validationGroups
     */
    public function __construct(string $dataClass, array $validationGroups)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'owl.tax_rate.name',
            ])
            ->add('amount', PercentType::class, [
                'label' => 'owl.tax_rate.amount',
                'scale' => 3,
                'required' => false,
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'owl.tax_rate.start_date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'owl.tax_rate.end_date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice_tax_rate';
    }
}
