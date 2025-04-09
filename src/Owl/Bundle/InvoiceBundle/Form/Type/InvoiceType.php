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
                'required' => true,
            ])
            ->add('issueDate', DateType::class, [
                'required' => false,
                'label' => 'owl.invoice.issue_date',
                'widget' => 'single_text',
            ])
            ->add('transactionDate', DateType::class, [
                'required' => false,
                'label' => 'owl.invoice.transaction_date',
                'widget' => 'single_text',
            ])
            ->add('paymentDate', DateType::class, [
                'required' => false,
                'label' => 'owl.invoice.payment_date',
                'widget' => 'single_text',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice';
    }
}
