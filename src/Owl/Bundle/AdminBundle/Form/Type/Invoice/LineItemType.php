<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type\Invoice;

use Owl\Bundle\InvoiceBundle\Form\Type\LineItemType as BaseLineItemType;
use Owl\Bundle\InvoiceBundle\Form\Type\Taxation\TaxRateChoiceType;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Repository\InvoiceTaxRateRepositoryInterface;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LineItemType extends AbstractType
{
    public function __construct(
        private EventSubscriberInterface $taxRateSnapshotEventSubscriber,
        private InvoiceTaxRateRepositoryInterface $invoiceTaxRateRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CompanyInterface $company */
        $company = $options['company'];
        $currency = $options['currency'];

        $builder
            ->add('unitPrice', MoneyType::class, [
                'required' => false,
                'label' => 'owl.invoice.line_item.unit_price',
                'currency' => $currency,
            ])
            ->add('totalPrice', MoneyType::class, [
                'required' => false,
                'label' => 'owl.invoice.line_item.total_price',
                'mapped' => false,
                'currency' => $currency,
            ])
            ->add('taxRate', TaxRateChoiceType::class, [
                'choices' => $company?->getZone() ? $this->invoiceTaxRateRepository->findByZone($company?->getZone()) : [],
                'label' => 'owl.invoice.line_item.tax_rate.label',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            if ($event->getForm()->has('totalPrice')) {
                $event->getForm()->get('totalPrice')->setData($event->getData()?->gettotalPrice());
            }
        });

        $builder->addEventSubscriber($this->taxRateSnapshotEventSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'company' => null,
            'currency' => null,
        ]);
    }

    public function getParent(): string
    {
        return BaseLineItemType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_admin_invoice_line_item';
    }
}
