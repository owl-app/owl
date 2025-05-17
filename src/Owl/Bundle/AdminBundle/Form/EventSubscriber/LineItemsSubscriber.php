<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Bundle\AdminBundle\Form\Type\Invoice\ExchangeRateSnapshot;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\LineItemType;
use Owl\Component\Company\Model\CompanyInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Sylius\Component\Currency\Repository\CurrencyRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class LineItemsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RepositoryInterface $companyRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function postSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var InvoiceInterface $invoice */
        $invoice = $event->getData();

        if ($invoice === null || $invoice->getCurrency() === null) {
            return;
        }

        $this->createLineItemsForm($form, $invoice->getCurrency()->getCode(), $invoice->getCompany());
    }

    public function preSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $invoice = $event->getData();

        if ($invoice === null || empty($invoice['company']) || empty($invoice['currency']) || $form->has('lineItems')) {
            return;
        }

        $company = $this->companyRepository->find($invoice['company']);

        $this->createLineItemsForm($form, $invoice['currency'], $company);
    }

    public function createLineItemsForm(FormInterface $form, string $currencyCode, CompanyInterface $company): void
    {
        $form->add('lineItems', LiveCollectionType::class, [
            'entry_type' => LineItemType::class,
            'entry_options' => [
                'company' => $company,
                'currency' => $currencyCode,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'button_add_options' => [
                'label' => 'owl.ui.invoice.add_line_item',
                'attr' => [
                    'class' => 'btn btn-secondary w-auto ps-5 pe-5 mt-3',
                ],
            ],
            'button_delete_options' => [
                'label' => false,
                'row_attr' => [
                    'class' => 'mb-0',
                ]
            ],
        ]);
    }

}
