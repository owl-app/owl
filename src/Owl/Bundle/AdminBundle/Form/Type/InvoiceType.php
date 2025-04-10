<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Owl\Bundle\InvoiceBundle\Form\Type\InvoiceType as BaseInvoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Owl\Component\Core\Model\Invoice\BuyerInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;

final class InvoiceType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('buyer', BuyerType::class, [
                'label' => false,
                'required' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var InvoiceInterface $invoice */
            $invoice = $event->getData();
            /** @var BuyerInterface $buyer */
            $buyer = $invoice->getBuyer();

            if ($buyer && $buyer->getContractor()) {
                $buyer->importContractorData($buyer->getContractor());
            }
        });
    }

    public function getParent(): string
    {
        return BaseInvoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice';
    }
}
