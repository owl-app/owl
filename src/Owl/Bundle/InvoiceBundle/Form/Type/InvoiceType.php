<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceType extends AbstractResourceType
{
    /**
     * @param string $dataClassInvoice FQCN
     * @param string $dataClassSerie FQCN
     * @param string[] $validationGroups
     */
    public function __construct(string $dataClassInvoice, private string $dataClassSerie, array $validationGroups = [])
    {
        parent::__construct($dataClassInvoice, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', HiddenType::class, [
                'error_bubbling' => false,
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
            ->add('duePaymentDate', DateType::class, [
                'label' => 'owl.invoice.due_payment_date',
                'widget' => 'single_text',
                'invalid_message' => 'owl.invoice.payment_date.invalid',
            ])
            ->add('serie', EntityType::class, [
                'label' => 'owl.invoice.serie',
                'class' => $this->dataClassSerie,
                'choice_label' => 'format',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_invoice';
    }
}
