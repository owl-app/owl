<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'label' => 'owl.invoice.number',
            ])
            ->add('issueDate', DateType::class, [
                'label' => 'owl.invoice.issue_date',
                'widget' => 'single_text',
                'invalid_message' => 'owl.invoice.issue_date.invalid',
            ])
            ->add('transactionDate', DateType::class, [
                'label' => 'owl.invoice.transaction_date',
                'widget' => 'single_text',
                'invalid_message' => 'owl.invoice.transaction_date.invalid',
            ])
            ->add('paymentDate', DateType::class, [
                'label' => 'owl.invoice.payment_date',
                'widget' => 'single_text',
                'invalid_message' => 'owl.invoice.payment_date.invalid',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice';
    }
}
